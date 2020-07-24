<?php

declare(strict_types=1);

namespace App\Caesar;

interface CaesarServerRpcInterface
{
    public function changedUserKeys(string $userId): void;

    public function updatedUserKeys(string $userId): void;
}
