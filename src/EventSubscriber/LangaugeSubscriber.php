<?php

namespace App\EventSubscriber;

use App\Constant\Language;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LangaugeSubscriber implements EventSubscriberInterface
{
    private $defaultLang;

    public function __construct(string $defaultLang = Language::ENGLISH)
    {
        $this->defaultLang = $defaultLang;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) return;

        // try to see if the language has been set as a _lang routing parameter
        if ($lang = $request->attributes->get('_lang')) {
            $request->getSession()->set('_lang', $lang);
        } else {
            // if no explicit language has been set on this request, use one from the session
            $request->setLocale($request->getSession()->get('_lang', $this->defaultLang));
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}