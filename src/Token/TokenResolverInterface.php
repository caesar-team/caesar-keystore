<?php

declare(strict_types=1);

namespace App\Token;

use Symfony\Component\HttpFoundation\Request;

interface TokenResolverInterface
{
    public function support(Request $request): bool;

    public function resolve(Request $request): ?string;
}
