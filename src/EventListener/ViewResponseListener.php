<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Response\ResponseFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ViewResponseListener implements EventSubscriberInterface
{
    private ResponseFactoryInterface $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public static function getSubscribedEvents()
    {
        // Must be executed before SensioFrameworkExtraBundle's listener
        return [
            KernelEvents::VIEW => ['onKernelView', 30],
        ];
    }

    public function onKernelView(ViewEvent $event): void
    {
        $response = $event->getResponse();
        if ($response instanceof Response) {
            return;
        }

        $event->setResponse(
            $this->responseFactory->createResponse($event->getControllerResult())
        );
    }
}
