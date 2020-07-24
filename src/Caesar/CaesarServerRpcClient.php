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

class CaesarServerRpcClient
{
    private const REQUEST_TIMEOUT = 5;
    private const DEFAULT_VERSION = '2.0';

    private ClientInterface $client;

    private LoggerInterface $logger;

    private string $host;

    private string $secret;

    private ?array $config = null;

    public function __construct(ClientInterface $client, LoggerInterface $logger, string $caesarRpcUrl, string $rpcSecret)
    {
        $this->client = $client;
        $this->logger = $logger;
        $this->host = $caesarRpcUrl;
        $this->secret = $rpcSecret;
    }

    public function request(string $method, array $params, array $options = []): array
    {
        try {
            $options = $this->getDefaultOptions($options);
            $options[RequestOptions::JSON] = [
                'jsonrpc' => self::DEFAULT_VERSION,
                'id' => uniqid(),
                'method' => $method,
                'params' => $params,
            ];

            $response = $this->client->request(
                'POST', '', $options
            );
        } catch (GuzzleException $exception) {
            throw new BadRequestException(
                sprintf('Bad Request "%s: %s", Error: "%s"', $method, json_encode($params), $exception->getMessage()),
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

        return $body;
    }

    private function getDefaultOptions(array $options = []): array
    {
        if (null === $this->config) {
            $this->config = [
                'base_uri' => $this->mapHost($this->host),
                'handler' => $this->createLoggingHandlerStack([
                    '[CaesarServerRPC] {method} {uri} HTTP/{version} {req_body}',
                    '[CaesarServerRPC] Response: {code} - {res_body}',
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
                RequestOptions::HEADERS => [
                    'Authorization' => sprintf('Bearer %s', $this->secret),
                ],
            ];
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
