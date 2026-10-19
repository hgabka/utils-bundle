<?php

namespace Hgabka\UtilsBundle\Tests\Traits;

use Hgabka\Doctrine\Translatable\Entity\TranslationTrait;
use Hgabka\Doctrine\Translatable\TranslatableInterface;
use Hgabka\Doctrine\Translatable\TranslationInterface;

class TraitTranslation implements TranslationInterface
{
    use TranslationTrait;

    private ?TranslatableInterface $translatable = null;
}
