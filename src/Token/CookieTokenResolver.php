<?php

declare(strict_types=1);

namespace App\Token;

use Symfony\Component\HttpFoundation\Request;

class CookieTokenResolver implements TokenResolverInterface
{
    public function support(Request $request): bool
    {
        return $request->cookies->has('token');
    }

    public function resolve(Request $request): ?string
    {
        if (!$this->support($request)) {
            return null;
        }

        return $request->cookies->get('token');
    }
}
