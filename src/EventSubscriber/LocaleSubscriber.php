<?php
namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if(str_contains($request->getPathInfo(),'/api/')){
            if (!$request->hasSession() || !$request->getSession()->isStarted()) {
                return; // skip session usage
            }
        }

        if (!$event->isMainRequest()) {
            return; // Ignore sub requests
        }

        if ($locale = $request->query->get('_locale')) {
            $request->setLocale($locale);
            $request->getSession()->set('_locale', $locale);
        } elseif ($request->getSession()->has('_locale')) {
            $request->setLocale($request->getSession()->get('_locale'));
        }

        $request->cookies->set('lang',$request->getSession()->get('_locale'));
    }

    public static function getSubscribedEvents(): array
    {
        // Set priority 20 so it runs before the default Symfony LocaleListener (priority 16)
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 20],
        ];
    }
}
