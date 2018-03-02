<?php

namespace Hgabka\UtilsBundle\Helper\Security\OAuth;

/**
 * Interface OAuthUserFinderInterface.
 */
interface OAuthUserFinderInterface
{
    /**
     * Tries to find a user in database based on email and googleId fields.
     * Returns null when nothing has been found.
     *
     * @param string email
     * @param string googleId
     * @param mixed $email
     * @param mixed $googleId
     *
     * @return mixed AbstractUser Implementation
     */
    public function findUserByGoogleSignInData($email, $googleId);
}
