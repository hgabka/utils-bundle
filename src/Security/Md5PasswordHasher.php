<?php

namespace Hgabka\UtilsBundle\Security;

use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class Md5PasswordHasher implements PasswordHasherInterface
{
    public function hash(string $plainPassword): string
    {
        return md5($plainPassword);
    }

    public function verify(string $hashedPassword, string $plainPassword): bool
    {
        return $hashedPassword === md5($plainPassword);
    }

    public function needsRehash(string $hashedPassword): bool
    {
        return true;
    }

}
