<?php

namespace Hgabka\UtilsBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollectionInterface;

class NestedTreeAdmin extends AbstractAdmin
{
    protected $accessMapping = [
        'reorder' => 'REORDER',
    ];

    public function toStringInTree($node)
    {
        if (isset($node['name'])) {
            return $node['name'];
        }

        if (isset($node['title'])) {
            return $node['title'];
        }
        if (isset($node['translations']['hu']['name'])) {
            return $node['translations']['hu']['name'];
        }

        if (isset($node['translations']['hu']['title'])) {
            return $node['translations']['hu']['title'];
        }
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->add('reorder', 'reOrder');
        $collection->add('subcreate', 'subCreate');

        $collection->remove('edit');
        $collection->remove('create');
    }
    
    protected function getAccessMapping(): array
    {
        return $this->accessMapping;
    }
}
