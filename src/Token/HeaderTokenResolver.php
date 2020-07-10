<?php

declare(strict_types=1);

namespace App\Token;

use Symfony\Component\HttpFoundation\Request;

class HeaderTokenResolver implements TokenResolverInterface
{
    public function support(Request $request): bool
    {
        return $request->headers->has('Authorization');
    }

    public function resolve(Request $request): ?string
    {
        if (!$this->support($request)) {
            return null;
        }

        $splitToken = explode(' ', $request->headers->get('Authorization', ''));

        return trim($splitToken[1] ?? $splitToken[0]);
    }
}
