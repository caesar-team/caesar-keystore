<?php

declare(strict_types=1);

namespace App\Token;

use Symfony\Component\HttpFoundation\Request;

class QueryTokenResolver implements TokenResolverInterface
{
    public function support(Request $request): bool
    {
        return $request->query->has('access_token');
    }

    public function resolve(Request $request): ?string
    {
        if (!$this->support($request)) {
            return null;
        }

        return $request->query->get('access_token');
    }
}
