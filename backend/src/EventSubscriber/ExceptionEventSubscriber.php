<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ExceptionEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => [
                ['onKernelException', 10],
            ],
        ];
    }

    /**
     * render json error.
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $statusCode = null;
        $message = null;
        if ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
            $message = $exception->getMessage();
        } elseif ($exception instanceof AccessDeniedException) {
            $statusCode = 403;
            $message = 'Access denied.';
        }

        if (null !== $statusCode) {
            $response = new JsonResponse([
                'code' => $statusCode,
                'message' => $message,
            ], $statusCode);
            $event->setResponse($response);
        }
    }
}
