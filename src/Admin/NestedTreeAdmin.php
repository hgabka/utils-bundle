<?php

namespace Hgabka\UtilsBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollection;

class NestedTreeAdmin extends AbstractAdmin
{
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

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('reorder', 'reOrder');
        $collection->add('subcreate', 'subCreate');

        $collection->remove('edit');
        $collection->remove('create');
    }
}
