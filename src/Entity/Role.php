<?php

namespace Hgabka\UtilsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Role\Role as BaseRole;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Role Entity.
 *
 * @ORM\Entity
 * @ORM\Table( name="hg_utils_roles" )
 * @UniqueEntity("role")
 */
class Role extends BaseRole
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", name="role", unique=true, length=70)
     */
    protected $role;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", name="name", length=70)
     */
    protected $name;

    /**
     * Populate the role field.
     *
     * @param string $role
     */
    public function __construct($role)
    {
        $this->role = $role;
    }

    /**
     * Return the string representation of the role entity.
     *
     * @return string
     */
    public function __toString() : string
    {
        return (string) $this->role;
    }

    /**
     * Return the role field.
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Modify the role field.
     *
     * @param string $role ROLE_FOO etc
     *
     * @return RoleInterface
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Return the name field.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Modify the name field.
     *
     * @param string $name
     *
     * @return RoleInterface
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}
