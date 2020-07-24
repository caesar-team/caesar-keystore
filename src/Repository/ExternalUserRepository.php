<?php

declare(strict_types=1);

namespace App\Repository;

use App\Caesar\CaesarServerInterface;
use App\Model\User;

class ExternalUserRepository implements UserRepositoryInterface
{
    private CaesarServerInterface $caesarServer;

    public function __construct(CaesarServerInterface $caesarServer)
    {
        $this->caesarServer = $caesarServer;
    }

    public function getUserByToken(string $token): ?User
    {
        $externalUser = $this->caesarServer->getSelfUser($token);
        if (null === $externalUser) {
            return null;
        }

        return User::createFromExternalUser($externalUser);
    }
}
