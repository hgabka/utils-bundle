<?php

namespace Hgabka\UtilsBundle\Tests\Fixtures;

use Hgabka\UtilsBundle\Doctrine\Type\AbstractEnumType;

class SuitType extends AbstractEnumType
{
    public const NAME = 'suit';

    public static function getEnumClass(): string
    {
        return Suit::class;
    }
}
