<?php

namespace Hgabka\UtilsBundle\Security;

use Doctrine\ORM\EntityManagerInterface;
use Hgabka\UtilsBundle\Model\UserInterface as BundleUserInterface;
use function is_subclass_of;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class UserProvider implements UserProviderInterface
{
    private $userClass;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, string $userClass)
    {
        $this->entityManager = $entityManager;
        $this->userClass = $userClass;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return $this->loadUserByUsername($identifier);
    }

    public function loadUserByUsername(string $username): UserInterface
    {
        $user = $this->findOneUserBy(['username' => $username]);

        if (!$user) {
            throw new AuthenticationException(sprintf('User with "%s" username does not exist.', $username));
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        assert($user instanceof BundleUserInterface);

        if (null === $reloadedUser = $this->findOneUserBy(['id' => $user->getId()])) {
            throw new AuthenticationException(sprintf('User with ID "%s" could not be reloaded.', $user->getId()));
        }

        return $reloadedUser;
    }

    public function supportsClass(string $class): bool
    {
        return $this->userClass === $class || is_subclass_of($class, $this->userClass);
    }

    private function findOneUserBy(array $options): ?UserInterface
    {
        return
            $this
                ->entityManager
                ->getRepository($this->userClass)
                ->findOneBy($options)
        ;
    }
}
