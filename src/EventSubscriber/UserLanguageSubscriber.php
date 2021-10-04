<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * Stores the language of the user in the session after the
 * login. This can be used by the LocaleSubscriber afterwards.
 */
class UserLanguageSubscriber implements EventSubscriberInterface
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        /* @var $user User */
        $user = $event->getAuthenticationToken()->getUser();
        echo 'name: ' . $user->getName();

        if ($user->getLang() !== null) {
            $this->requestStack->getSession()->set('_lang', $user->getLang());
        }
    }

    public static function getSubscribedEvents()
    {
        return [SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin'];
    }
}
