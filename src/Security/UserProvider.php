<?php

namespace Hgabka\UtilsBundle\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Hgabka\UtilsBundle\Entity\UserInterface as BundleUserInterface;
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

    public function loadUserByUsername(string $username): User
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
        return User::class === $class;
    }

    private function findOneUserBy(array $options): ?User
    {
        return
            $this
                ->entityManager
                ->getRepository($this->userClass)
                ->findOneBy($options)
        ;
    }
}
