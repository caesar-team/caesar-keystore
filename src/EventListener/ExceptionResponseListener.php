<?php

declare(strict_types=1);

namespace App\EventListener;

use App\View\ErrorView;
use Fourxxi\RestRequestError\Exception\InvalidRequestExceptionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class ExceptionResponseListener implements EventSubscriberInterface
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 10],
        ];
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        if ($exception instanceof InvalidRequestExceptionInterface) {
            return;
        }

        $view = new ErrorView($exception->getMessage(), $exception->getCode());

        $event->allowCustomResponseCode();

        $response = new JsonResponse();
        $response->setJson($this->serializer->serialize($view, JsonEncoder::FORMAT));
        $response->setStatusCode($this->getCode($exception));

        $event->setResponse($response);
    }

    private function getCode($exception): int
    {
        switch (true) {
            case $exception instanceof AccessDeniedException:
                return 403;
            case $exception instanceof NotFoundHttpException:
                return 404;
            default:
                return 500;
        }
    }
}
