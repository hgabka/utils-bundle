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
    private $tokenStorage;
    private $requestStack;
    private $eventDispatcher;
    private $sessionStrategy;
    private $firewallMap;
  
    public function __construct(TokenStorageInterface $tokenStorage, RequestStack $requestStack, EventDispatcherInterface $eventDispatcher, SessionAuthenticationStrategyInterface $sessionStrategy, FirewallMapInterface $firewallMap)
    {
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
        $this->eventDispatcher = $eventDispatcher;
        $this->sessionStrategy = $sessionStrategy;
        $this->firewallMap = $firewallMap;
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
        $config = $this->getFirewallConfig();
        $currentFirewallName = $config ? $config->getName() : null;
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
