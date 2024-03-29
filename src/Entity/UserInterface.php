<?php

namespace Hgabka\UtilsBundle\Entity;

interface UserInterface
{
    public const ROLE_DEFAULT = 'ROLE_USER';

    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): ?int;

    /**
     * Set id.
     *
     * @param int $id The unique identifier
     *
     * @return AbstractEntity
     */
    public function setId(?int $id);

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored in a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return array<Role|string> The user roles
     */
    public function getRoles(): array;

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return null|string The encoded password if any
     */
    public function getPassword(): ?string;

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return null|string The salt
     */
    public function getSalt(): ?string;

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername(): ?string;

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials(): void;
}
