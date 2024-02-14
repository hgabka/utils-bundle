<?php

namespace Hgabka\UtilsBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

abstract class AbstractNestedTreeEntity implements NestedTreeEntityInterface, EntityInterface
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return bool
     */
    public function canHaveChildren(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isDeleteable(): bool
    {
        return !$this->isRoot();
    }

    public function isRoot(): bool
    {
        return null === $this->getParent();
    }

    /**
     * @return AbstractNestedTreeEntity[]
     */
    public function getParents(): array
    {
        $parent = $this->getParent();
        $parents = [];
        while (null !== $parent) {
            $parents[] = $parent;
            $parent = $parent->getParent();
        }

        return array_reverse($parents);
    }

    abstract public function getParent(): ?self;

    abstract public function getLeft(): ?int;

    abstract public function getRight(): ?int;

    abstract public function getChildren(): Collection|array|null;

    public function getOptionLabel(string $textIndent = '-'): string
    {
        $indent = str_repeat($textIndent, $this->getLevel());

        return $indent . ' ' . $this->getTitle('hu');
    }
}
