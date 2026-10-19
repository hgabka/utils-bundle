<?php

namespace Hgabka\UtilsBundle\Tests\Fixtures;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Product
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    public ?int $id = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $name = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    public ?int $price = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    public ?\DateTime $createdAt = null;
}
