<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AccessDeniedListener implements EventSubscriberInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // the priority must be greater than the Security HTTP
            // ExceptionListener, to make sure it's called before
            // the default exception listener
            KernelEvents::EXCEPTION => ['onKernelException', 2],
        ];
    }

    public function onKernelException(ExceptionEvent $event): ?RedirectResponse
    {
        $exception = $event->getThrowable();
        if (!$exception instanceof AccessDeniedException) {
            return null;
        }

        return new RedirectResponse($this->urlGenerator->generate('app_error_page'));
        // optionally set the custom response
        // $event->setResponse(new Response(null, 403));
        //
        // or stop propagation (prevents the next exception listeners from being called)
        // $event->stopPropagation();
    }
}
