<?php

namespace Hgabka\UtilsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Role Entity.
 *
 * @ORM\Entity
 * @ORM\Table( name="hg_utils_roles" )
 * @UniqueEntity("role")
 */
#[ORM\Entity]
#[ORM\Table(name: 'hg_utils_roles')]
#[UniqueEntity('role')]
class Role
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", name="role", unique=true, length=70)
     */
    #[ORM\Column(name: 'role', type: 'string', unique: true, length: 70)]
    #[Assert\NotBlank]
    protected ?string $role = null;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", name="name", length=70)
     */
    #[ORM\Column(name: 'name', type: 'string', length: 70)]
    #[Assert\NotBlank]
    protected ?string $name = null;

    /**
     * Populate the role field.
     *
     * @param string $role
     */
    public function __construct(?string $role)
    {
        $this->role = $role;
    }

    /**
     * Return the string representation of the role entity.
     */
    public function __toString(): string
    {
        return (string) $this->role;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setRole(?string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
