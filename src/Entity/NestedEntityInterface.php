<?php

namespace Hgabka\UtilsBundle\Entity;

interface NestedEntityInterface
{
    public function getId();

    public function getParent();

    public function getLeft();

    public function getRight();

    public function getChildren();

    public function canHaveChildren();

    public function isDeleteable();
}
