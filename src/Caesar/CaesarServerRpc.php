<?php

declare(strict_types=1);

namespace App\Caesar;

use Psr\Log\LoggerInterface;

class CaesarServerRpc implements CaesarServerRpcInterface
{
    private CaesarServerRpcClient $client;

    private LoggerInterface $logger;

    public function __construct(CaesarServerRpcClient $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    public function changedUserKeys(string $userId): void
    {
        $this->client->request('changed-user-keys', ['user' => $userId]);
    }

    public function updatedUserKeys(string $userId): void
    {
        $this->client->request('updated-user-keys', ['user' => $userId]);
    }
}
