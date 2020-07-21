<?php

declare(strict_types=1);

namespace App\Token;

use Symfony\Component\HttpFoundation\Request;

class CompositeTokenResolver implements TokenResolverInterface
{
    /**
     * @var TokenResolverInterface[]
     */
    private array $resolvers;

    public function __construct(TokenResolverInterface ...$resolvers)
    {
        $this->resolvers = $resolvers;
    }

    public function support(Request $request): bool
    {
        foreach ($this->resolvers as $resolver) {
            if (!$resolver->support($request)) {
                continue;
            }

            return true;
        }

        return false;
    }

    public function resolve(Request $request): ?string
    {
        foreach ($this->resolvers as $resolver) {
            if (null === $resolver->resolve($request)) {
                continue;
            }

            return $resolver->resolve($request);
        }

        return null;
    }
}
