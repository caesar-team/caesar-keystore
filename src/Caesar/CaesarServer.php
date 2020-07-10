<?php

declare(strict_types=1);

namespace App\Caesar;

use App\Exception\BadRequestException;
use App\View\ExternalUserView;
use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;

class CaesarServer implements CaesarServerInterface
{
    private CaesarServerClient $client;

    private LoggerInterface $logger;

    public function __construct(CaesarServerClient $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    public function getSelfUser(string $token): ?ExternalUserView
    {
        try {
            $response = $this->client->request('GET', '/api/users/self', [
                RequestOptions::HEADERS => [
                    'Authorization' => sprintf('Bearer %s', $token),
                ],
            ]);

            return ExternalUserView::createFromResponse($response);
        } catch (BadRequestException $exception) {
            $this->logger->info(sprintf(
                'Failed to get self user, Server Error: %s, Trace: %s',
                $exception->getMessage(),
                $exception->getTraceAsString()
            ));
        } catch (\Exception $exception) {
            $this->logger->warning(sprintf(
                'Failed to get self user, Error: %s, Trace: %s',
                $exception->getMessage(),
                $exception->getTraceAsString()
            ));
        }

        return null;
    }
}
