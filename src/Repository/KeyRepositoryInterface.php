<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Key;

interface KeyRepositoryInterface
{
    public function save(Key $key): void;

    public function getKeyByEmail(string $email): ?Key;

    /**
     * @param string[] $emails
     *
     * @return Key[]
     */
    public function getKeysByEmails(array $emails): array;
}
