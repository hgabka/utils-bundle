<?php

/*
 * This file is part of the HgabkaUtilsBundle package.
 *
 */

namespace Hgabka\UtilsBundle\Util;

class TokenGenerator implements TokenGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}
