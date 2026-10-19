<?php

namespace Hgabka\UtilsBundle\Tests\Command;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;

class CommandsTest extends TestCase
{
    public static function commandClasses(): iterable
    {
        foreach (glob(__DIR__ . '/../../src/Command/*Command.php') as $file) {
            yield basename($file, '.php') => ['Hgabka\\UtilsBundle\\Command\\' . basename($file, '.php')];
        }
    }

    #[DataProvider('commandClasses')]
    public function testEveryCommandClassLoadsAndIsNamed(string $class): void
    {
        $this->assertTrue(class_exists($class), $class);
        $reflection = new \ReflectionClass($class);
        $this->assertTrue($reflection->isSubclassOf(Command::class));
        if (!$reflection->isAbstract()) {
            $attributes = $reflection->getAttributes(AsCommand::class);
            $this->assertNotEmpty($attributes, $class . ' needs #[AsCommand]');
        }
    }
}
