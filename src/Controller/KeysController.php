<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class KeysController extends AbstractController
{
    /**
     * @Route(path="/", methods={"GET"})
     */
    public function index(): JsonResponse
    {
        return new JsonResponse();
    }
}
