<?php

declare(strict_types=1);

namespace App\Caesar;

use App\View\ExternalUserView;

interface CaesarServerInterface
{
    public function getSelfUser(string $token): ?ExternalUserView;
}
