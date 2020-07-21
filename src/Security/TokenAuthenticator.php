<?php

declare(strict_types=1);

namespace App\Security;

use App\Repository\UserRepositoryInterface;
use App\Token\TokenResolverInterface;
use App\View\ErrorView;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    private SerializerInterface $serializer;

    private UserRepositoryInterface $repository;

    private TokenResolverInterface $tokenResolver;

    public function __construct(
        UserRepositoryInterface $repository,
        SerializerInterface $serializer,
        TokenResolverInterface $tokenResolver
    ) {
        $this->repository = $repository;
        $this->serializer = $serializer;
        $this->tokenResolver = $tokenResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $view = new ErrorView('Authentication Required', Response::HTTP_UNAUTHORIZED);

        $response = new JsonResponse();
        $response->setJson($this->serializer->serialize($view, JsonEncoder::FORMAT));
        $response->setStatusCode(Response::HTTP_UNAUTHORIZED);

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request)
    {
        return $this->tokenResolver->support($request);
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials(Request $request)
    {
        return (string) $this->tokenResolver->resolve($request);
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (empty($credentials)) {
            return null;
        }

        return $this->repository->getUserByToken($credentials);
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $view = new ErrorView(strtr($exception->getMessageKey(), $exception->getMessageData()), Response::HTTP_UNAUTHORIZED);

        $response = new JsonResponse();
        $response->setJson($this->serializer->serialize($view, JsonEncoder::FORMAT));
        $response->setStatusCode(Response::HTTP_UNAUTHORIZED);

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsRememberMe()
    {
        return false;
    }
}
