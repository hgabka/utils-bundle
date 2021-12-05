<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hgabka\UtilsBundle\Util;

use Hgabka\UtilsBundle\Model\UserInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\SelfSaltingEncoderInterface;

/**
 * Class updating the hashed password in the user when there is a new password.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class PasswordUpdater implements PasswordUpdaterInterface
{
    private $hasher;

    public function __construct(PasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function hashPassword(UserInterface $user)
    {
        $plainPassword = $user->getPlainPassword();

        if (0 === strlen($plainPassword)) {
            return;
        }


        $hashedPassword = $this->hasher->hash($plainPassword);
        $user->setPassword($hashedPassword);
        $user->eraseCredentials();
    }
}
