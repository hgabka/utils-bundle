<?php

namespace Hgabka\UtilsBundle\Tests\Fixtures;

use Doctrine\Persistence\Proxy;

/** Looks like a Doctrine proxy of \ArrayObject. */
class ProxyStub extends \ArrayObject implements Proxy
{
    public function __load(): void
    {
    }

    public function __isInitialized(): bool
    {
        return true;
    }
}
