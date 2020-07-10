<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/keys")
 */
class KeysController extends AbstractController
{
    /**
     * @Route(path="", name="get_public_keys", methods={"GET"})
     */
    public function index(): JsonResponse
    {
        return new JsonResponse();
    }
}
