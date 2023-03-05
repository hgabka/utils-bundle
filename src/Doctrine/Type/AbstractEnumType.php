<?php

namespace Hgabka\UtilsBundle\Doctrine\Type;

use BackedEnum;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

abstract class AbstractEnumType extends Type implements EnumTypeInterface
{
    public function getName(): string
    {
        return self::NAME;
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'TEXT';
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value instanceof BackedEnum) {
            return $value->value;
        }

        return null;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?BackedEnum
    {
        if (false === enum_exists($this->getEnumClass(), true)) {
            throw new \LogicException('This class should be an enum');
        }
        
        if (null === $value) {
            return null;
        }

        return $this::getEnumClass()::tryFrom($value);
    }
}
