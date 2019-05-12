<?php

namespace Hgabka\UtilsBundle\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class LongblobType extends Type
{
    const TYPE = 'hg_utils_longblob';

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getBlobTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        return (string) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        return (string) $value;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::TYPE;
    }
}
