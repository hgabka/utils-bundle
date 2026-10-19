<?php

namespace Hgabka\UtilsBundle\Tests\Doctrine;

use Doctrine\DBAL\Platforms\SQLitePlatform;
use Hgabka\UtilsBundle\Tests\Fixtures\Suit;
use Hgabka\UtilsBundle\Tests\Fixtures\SuitType;
use PHPUnit\Framework\TestCase;

class EnumTypeTest extends TestCase
{
    private SuitType $type;

    private SQLitePlatform $platform;

    protected function setUp(): void
    {
        $this->type = new SuitType();
        $this->platform = new SQLitePlatform();
    }

    public function testEnumsAreStoredByValue(): void
    {
        $this->assertSame('hearts', $this->type->convertToDatabaseValue(Suit::Hearts, $this->platform));
        $this->assertNull($this->type->convertToDatabaseValue(null, $this->platform));
        $this->assertNull($this->type->convertToDatabaseValue('hearts', $this->platform));
    }

    public function testValuesAreHydratedToEnums(): void
    {
        $this->assertSame(Suit::Spades, $this->type->convertToPHPValue('spades', $this->platform));
        $this->assertNull($this->type->convertToPHPValue(null, $this->platform));
        $this->assertNull($this->type->convertToPHPValue('unknown', $this->platform));
    }

    public function testColumnDeclaration(): void
    {
        $this->assertSame('TEXT', $this->type->getSQLDeclaration([], $this->platform));
        $this->assertSame('suit', $this->type->getName());
        $this->assertSame(Suit::class, SuitType::getEnumClass());
    }
}
