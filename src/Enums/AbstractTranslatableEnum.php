<?php

namespace Hgabka\UtilsBundle\Enums;

enum AbstractTranslatableEnum implements TranslatableEnumInterface
{
    public function getTranslationPrefix(): string
    {
        return '';
    }
}
