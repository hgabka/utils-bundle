<?php

namespace Hgabka\UtilsBundle\Security;

use Symfony\Bundle\SecurityBundle\Security\FirewallConfig;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\FirewallMapInterface;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class LoginManager
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly RequestStack $requestStack,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly SessionAuthenticationStrategyInterface $sessionStrategy,
        private readonly FirewallMapInterface $firewallMap
    ) {
    }

    public function getFirewallConfig(): ?FirewallConfig
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            return null;
        }

        return $this->firewallMap->getFirewallConfig($request);
    }

    public function loginUser(UserInterface $user, string $firewallName)
    {
        $currentFirewallName = $this->getFirewallConfig()?->getName();
        $token = new UsernamePasswordToken($user, $firewallName, $user->getRoles());
        $request = $this->requestStack->getCurrentRequest();

        if (null === $currentFirewallName || $firewallName === $currentFirewallName) {
            $this->tokenStorage->setToken($token);
            if (null !== $request) {
                $this->sessionStrategy->onAuthentication($request, $token);
                $event = new InteractiveLoginEvent($request, $token);
                $this->eventDispatcher->dispatch($event, SecurityEvents::INTERACTIVE_LOGIN);
            }
        } else {
            if (null !== $request) {
                $request->getSession()->set('_security_' . $firewallName, serialize($token));
            }
        }
    }
}
