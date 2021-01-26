<?php

namespace Hgabka\UtilsBundle\Helper;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Adds the user information to the context of the record which will be logged.
 */
class UserProcessor
{
    /**
     * Use container else we have a continous loop in our dependency.
     *
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @var array
     */
    private $record = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public function processRecord(array $record)
    {
        if (null === $this->user) {
            // @var TokenStorageInterface $securityTokenStorage
            $securityTokenStorage = $this->container->get('security.token_storage');
            if ((null !== $securityTokenStorage) && (null !== $securityTokenStorage->getToken()) && ($securityTokenStorage->getToken()->getUser() instanceof \Symfony\Component\Security\Core\User\AdvancedUserInterface)) {
                $this->user = $securityTokenStorage->getToken()->getUser();
                $this->record['extra']['user']['username'] = $this->user->getUsername();
                $this->record['extra']['user']['roles'] = $this->user->getRoles();
                $this->record['extra']['user']['is_account_non_expired'] = $this->user->isAccountNonExpired();
                $this->record['extra']['user']['is_account_non_locked'] = $this->user->isAccountNonLocked();
                $this->record['extra']['user']['is_credentials_non_expired'] = $this->user->isCredentialsNonExpired();
                $this->record['extra']['user']['is_enabled'] = $this->user->isEnabled();
            }
        }

        return array_merge($record, $this->record);
    }
}
