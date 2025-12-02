<?php

namespace Hgabka\UtilsBundle;

use Doctrine\DBAL\Types\Type;
use Hgabka\UtilsBundle\Doctrine\Type\LongblobType;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HgabkaUtilsBundle extends Bundle
{
    public function boot(): void
    {
        foreach ($this->container->getParameter('doctrine.entity_managers') as $name => $serviceName) {
            $em = $this->container->get($serviceName);
            if (!Type::hasType(LongblobType::TYPE)) {
                Type::addType(LongblobType::TYPE, LongblobType::class);
                $em->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('longblob', LongblobType::TYPE);
            }
        }
    }
}
