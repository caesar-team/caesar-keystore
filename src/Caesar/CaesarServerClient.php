<?php

declare(strict_types=1);

namespace App\Caesar;

use App\Exception\BadRequestException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\TransferStats;
use Psr\Log\LoggerInterface;

class CaesarServerClient
{
    private const REQUEST_TIMEOUT = 5;

    private ClientInterface $client;

    private LoggerInterface $logger;

    private string $host;

    private ?string $token = null;

    private ?array $config = null;

    public function __construct(ClientInterface $client, LoggerInterface $logger, string $caesarHost)
    {
        $this->client = $client;
        $this->logger = $logger;
        $this->host = $caesarHost;
    }

    public function request(string $method, string $uri, array $options = []): array
    {
        try {
            $response = $this->client->request(
                $method, $uri, $this->getDefaultOptions($options)
            );
        } catch (GuzzleException $exception) {
            throw new BadRequestException(
                sprintf('Bad Request "%s: %s", Error: "%s"', $method, $uri, $exception->getMessage()),
                $exception->getCode(),
                $exception
            );
        }

        $response->getBody()->rewind();
        $content = $response->getBody()->getContents();

        $body = json_decode($content, true);
        if (!is_array($body)) {
            throw new BadRequestException(sprintf('Response "%s: %s" should be json format, error: "%s"', $method, $uri, json_last_error_msg()));
        }

        if (null !== ($body['code'] ?? null) || true !== ($body['error'] ?? true)) {
            throw new BadRequestException(sprintf('Bad Request "%s: %s", Error: "%s"', $method, $uri, json_encode($body)));
        }

        return $body;
    }

    private function getDefaultOptions(array $options = []): array
    {
        if (null === $this->config) {
            $this->config = [
                'base_uri' => $this->mapHost($this->host),
                'handler' => $this->createLoggingHandlerStack([
                    '[CaesarServer] {method} {uri} HTTP/{version} {req_body}',
                    '[CaesarServer] Response: {code} - {res_body}',
                ]),
                RequestOptions::ON_STATS => function (TransferStats $stats) {
                    $this->logger->info(sprintf(
                        '[CaesarServer] %s %s %sms',
                        $stats->getRequest()->getMethod(),
                        $stats->getEffectiveUri(),
                        $stats->getTransferTime()
                    ));
                },
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::TIMEOUT => self::REQUEST_TIMEOUT,
            ];

            if (null !== $this->token) {
                $this->config[RequestOptions::HEADERS] = [
                    'Authorization' => sprintf('Bearer %s', $this->token),
                ];
            }
        }

        return array_merge_recursive($this->config, $options);
    }

    private function createLoggingHandlerStack(array $messageFormats): HandlerStack
    {
        $stack = HandlerStack::create();
        foreach ($messageFormats as $messageFormat) {
            $stack->unshift($this->createGuzzleLoggingMiddleware($messageFormat));
        }

        return $stack;
    }

    private function createGuzzleLoggingMiddleware(string $messageFormat): callable
    {
        return Middleware::log($this->logger, new MessageFormatter($messageFormat));
    }

    private function mapHost(string $host): string
    {
        if (null === parse_url($host, PHP_URL_SCHEME)) {
            return sprintf('http://%s', $host);
        }

        return $host;
    }
}
