<?php

namespace Hgabka\UtilsBundle\Entity;

interface NestedTreeEntityInterface
{
    public function getId();

    public function getParent();

    public function getLeft();

    public function getRight();

    public function getChildren();

    public function canHaveChildren();

    public function isDeleteable();
    
    public function getOptionLabel(string $textIndent = '-'): string;
}
