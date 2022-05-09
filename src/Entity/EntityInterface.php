<?php

namespace Hgabka\UtilsBundle\Entity;

interface EntityInterface
{
    public function getId(): ?int;

    public function setId(?int $id): self;
}
