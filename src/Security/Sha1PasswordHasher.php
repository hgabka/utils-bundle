<?php

namespace Hgabka\UtilsBundle\Security;

use Symfony\Component\PasswordHasher\LegacyPasswordHasherInterface;

class Sha1PasswordHasher implements LegacyPasswordHasherInterface
{
    public function hash(string $plainPassword, string $salt = null): string
    {
        return sha1($salt.$plainPassword);
    }

    public function verify(string $hashedPassword, string $plainPassword, string $salt = null): bool
    {
        return $hashedPassword === sha1($salt.$plainPassword);
    }

    public function needsRehash(string $hashedPassword): bool
    {
        return true;
    }

}
