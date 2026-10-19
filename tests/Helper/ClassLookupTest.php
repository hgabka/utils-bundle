<?php

namespace Hgabka\UtilsBundle\Tests\Helper;

use Hgabka\UtilsBundle\Helper\ClassLookup;
use Hgabka\UtilsBundle\Tests\Fixtures\ProxyStub;
use Hgabka\UtilsBundle\Helper\RequestHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class ClassLookupTest extends TestCase
{
    public function testClassOfAPlainObject(): void
    {
        $this->assertSame(\stdClass::class, ClassLookup::getClass(new \stdClass()));
    }

    public function testProxyClassesResolveToTheirParent(): void
    {
        $this->assertSame(\ArrayObject::class, ClassLookup::getClass(new ProxyStub()));
    }

    public function testNonObjectsAreRejected(): void
    {
        $this->assertFalse(ClassLookup::getClass('string'));
    }

    public function testClassNameWithoutNamespace(): void
    {
        $this->assertSame('ClassLookupTest', ClassLookup::getClassName(self::class));
        $this->assertSame('stdClass', ClassLookup::getClassName(new \stdClass()));
    }

    public function testRequestHelperReadsQueryRequestAndAttributes(): void
    {
        $request = Request::create('/?q=1', 'POST', ['p' => '2']);
        $request->attributes->set('a', '3');

        $this->assertSame('1', RequestHelper::get($request, 'q'));
        $this->assertSame('2', RequestHelper::get($request, 'p'));
        $this->assertSame('3', RequestHelper::get($request, 'a'));
        $this->assertSame('dflt', RequestHelper::get($request, 'missing', 'dflt'));
    }
}
