<?php

namespace Hgabka\UtilsBundle\Security;

use Symfony\Component\PasswordHasher\Exception\InvalidPasswordException;
use Symfony\Component\PasswordHasher\Hasher\CheckPasswordLengthTrait;
use Symfony\Component\PasswordHasher\LegacyPasswordHasherInterface;

class Sha1PasswordHasher implements LegacyPasswordHasherInterface
{
    use CheckPasswordLengthTrait;
    
    public function hash(string $plainPassword, string $salt = null): string
    {
        if ($this->isPasswordTooLong($plainPassword)) {
            throw new InvalidPasswordException();
        }
        
        return sha1($salt.$plainPassword);
    }

    public function verify(string $hashedPassword, string $plainPassword, string $salt = null): bool
    {
        if ('' === $plainPassword || $this->isPasswordTooLong($plainPassword)) {
            return false;
        }
        
        return $hashedPassword === sha1($salt.$plainPassword);
    }

    public function needsRehash(string $hashedPassword): bool
    {
        return true;
    }

}
