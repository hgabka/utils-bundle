<?php

namespace HG\UtilsBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class HGEventSubscriber implements EventSubscriberInterface
{
    private $defaultLocale;
    private $sessionFromRequest;
    private $sessionParamName;

    public function __construct($defaultLocale = 'hu', $sessionFromRequest = false, $sessionParamName = 'symfony')
    {
        $this->defaultLocale = $defaultLocale;
        $this->sessionFromRequest = $sessionFromRequest;
        $this->sessionParamName = $sessionParamName;
    }

    public static function getSubscribedEvents()
    {
       return array(
            // must be registered before the default Locale listener
            KernelEvents::REQUEST => array(
              array('setLocale', 17),
              array('setSessionId', 18)
            ),
        );
    }

    public function setLocale(GetResponseEvent $event)
    {
      $request = $event->getRequest();
      if (!$request->hasPreviousSession())
      {
        return;
      }

      if ($locale = $request->attributes->get('_locale'))
      {
        $request->getSession()->set('_locale', $locale);
      }
      else
      {
        $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
      }
    }

    public function setSessionId(GetResponseEvent $event)
    {
      $request = $event->getRequest();
      if (!$this->sessionFromRequest)
      {
        return;
      }

      $request = $event->getRequest();

      if ($request->request->has($this->sessionParamName))
      {
        $request->cookies->set(session_name(), 1);
        session_id($request->request->get($this->sessionParamName));
        $request->request->remove($this->sessionParamName);
      }
    }
}

