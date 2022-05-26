<?php

namespace Hgabka\UtilsBundle\Security;

use Hgabka\UtilsBundle\Model\AbstractUser;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserChecker implements UserCheckerInterface
{
    public function __construct(private readonly TranslatorInterface $translator) {}

    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof AbstractUser) {
            return;
        }

        if (!$user->isEnabled()) {
            // the message passed to this exception is meant to be displayed to the user
            throw new CustomUserMessageAccountStatusException($this->translator->trans('hg_utils.security.error_not_enabled'));
        }
    }
    
    public function checkPostAuth(UserInterface $user): void {}
}
