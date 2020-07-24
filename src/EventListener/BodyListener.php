<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class BodyListener implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 10],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$this->isDecodeable($request)) {
            return;
        }

        $content = $request->getContent();
        if (!empty($content)) {
            $data = json_decode($content, true);
            if (is_array($data)) {
                $request->request = new ParameterBag($data);
            } else {
                throw new BadRequestHttpException('Invalid message received');
            }
        }
    }

    private function isDecodeable(Request $request): bool
    {
        if (!in_array($request->getMethod(), [Request::METHOD_POST, Request::METHOD_PUT, Request::METHOD_PATCH, Request::METHOD_DELETE])) {
            return false;
        }

        return !$this->isFormRequest($request);
    }

    private function isFormRequest(Request $request): bool
    {
        $contentTypeParts = explode(';', $request->headers->get('Content-Type', ''));

        if (isset($contentTypeParts[0])) {
            return in_array($contentTypeParts[0], ['multipart/form-data', 'application/x-www-form-urlencoded']);
        }

        return false;
    }
}
