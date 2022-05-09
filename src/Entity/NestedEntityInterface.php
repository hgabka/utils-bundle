<?php

namespace Hgabka\UtilsBundle\Entity;

use Doctrine\Common\Collections\Collection;

interface NestedEntityInterface
{
    public function getId(): ?int;

    public function getParent(): ?self;

    public function getLeft(): ?int;

    public function getRight(): ?int;

    public function getChildren(): Collection|array|null;

    public function canHaveChildren(): bool;

    public function isDeleteable(): bool;
}
