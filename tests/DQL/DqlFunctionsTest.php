<?php

namespace Hgabka\UtilsBundle\Tests\DQL;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Hgabka\UtilsBundle\DQL\Cast;
use Hgabka\UtilsBundle\DQL\CharLength;
use Hgabka\UtilsBundle\DQL\Date;
use Hgabka\UtilsBundle\DQL\Day;
use Hgabka\UtilsBundle\DQL\First;
use Hgabka\UtilsBundle\DQL\Format;
use Hgabka\UtilsBundle\DQL\IfElse;
use Hgabka\UtilsBundle\DQL\IfNull;
use Hgabka\UtilsBundle\DQL\Instr;
use Hgabka\UtilsBundle\DQL\Left;
use Hgabka\UtilsBundle\DQL\Month;
use Hgabka\UtilsBundle\DQL\Rand;
use Hgabka\UtilsBundle\DQL\Regexp;
use Hgabka\UtilsBundle\DQL\Repeat;
use Hgabka\UtilsBundle\DQL\Round;
use Hgabka\UtilsBundle\DQL\Substr;
use Hgabka\UtilsBundle\DQL\Year;
use Hgabka\UtilsBundle\Tests\Fixtures\Product;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DqlFunctionsTest extends TestCase
{
    private function entityManager(): EntityManager
    {
        $config = ORMSetup::createAttributeMetadataConfiguration([__DIR__ . '/../Fixtures'], true);
        if (method_exists($config, 'enableNativeLazyObjects')) {
            $config->enableNativeLazyObjects(true);
        }
        foreach (self::functions() as [$name, $class, , $type]) {
            if ('string' === $type) {
                $config->addCustomStringFunction($name, $class);
            } else {
                $config->addCustomNumericFunction($name, $class);
            }
        }

        $connection = $this->createStub(Connection::class);
        $connection->method('getConfiguration')->willReturn(new \Doctrine\DBAL\Configuration());

        return new EntityManager($connection, $config);
    }

    /** @return array<string, array{string, class-string, string, string}> */
    public static function functions(): array
    {
        return [
            'CAST' => ['CAST', Cast::class, 'CAST(p.price AS CHAR)', 'string'],
            'RAND' => ['RAND', Rand::class, 'RAND()', 'numeric'],
            'IF' => ['IF', IfElse::class, 'IF(p.price > 1, 2, 3)', 'string'],
            'IFNULL' => ['IFNULL', IfNull::class, 'IFNULL(p.price, 0)', 'string'],
            'REPEAT' => ['REPEAT', Repeat::class, "REPEAT(p.name, 3)", 'string'],
            'INSTR' => ['INSTR', Instr::class, "INSTR(p.name, 'a')", 'string'],
            'DATE' => ['DATE', Date::class, 'DATE(p.createdAt)', 'string'],
            'ROUND' => ['ROUND', Round::class, 'ROUND(p.price, 2)', 'numeric'],
            'FORMAT' => ['FORMAT', Format::class, 'FORMAT(p.price, 2)', 'numeric'],
            'REGEXP' => ['REGEXP', Regexp::class, "REGEXP(p.name, 'a+')", 'string'],
            'SUBSTR' => ['SUBSTR', Substr::class, 'SUBSTR(p.name, 1, 2)', 'string'],
            'LEFT' => ['LEFT', Left::class, 'LEFT(p.name, 2)', 'string'],
            'CHAR_LENGTH' => ['CHAR_LENGTH', CharLength::class, 'CHAR_LENGTH(p.name)', 'string'],
            'FIRST' => ['FIRST', First::class, 'FIRST(SELECT p2.name FROM ' . Product::class . ' p2)', 'string'],
            'YEAR' => ['YEAR', Year::class, 'YEAR(p.createdAt)', 'string'],
            'MONTH' => ['MONTH', Month::class, 'MONTH(p.createdAt)', 'string'],
            'DAY' => ['DAY', Day::class, 'DAY(p.createdAt)', 'string'],
        ];
    }

    #[DataProvider('functions')]
    public function testFunctionsAreRegisteredUnderTheirNames(string $name, string $class, string $expression, string $type): void
    {
        $this->assertSame($name, $class::FUNCTION_NAME);
        $this->assertTrue(is_subclass_of($class, FunctionNode::class));
    }

    #[DataProvider('functions')]
    public function testFunctionsCanBeParsedInDql(string $name, string $class, string $expression, string $type): void
    {
        $query = new Query($this->entityManager());
        $query->setDQL('SELECT ' . $expression . ' FROM ' . Product::class . ' p');

        $ast = $query->getAST();

        $node = $ast->selectClause->selectExpressions[0]->expression;
        $this->assertInstanceOf($class, $node);
    }

    public function testAFunctionCanBeUsedInAConditionToo(): void
    {
        $query = new Query($this->entityManager());
        $query->setDQL('SELECT p FROM ' . Product::class . " p WHERE IFNULL(p.price, 0) > 5 AND CHAR_LENGTH(p.name) = 3");

        $this->assertNotNull($query->getAST()->whereClause);
    }
}
