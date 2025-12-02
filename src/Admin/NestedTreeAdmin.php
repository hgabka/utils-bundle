<?php

namespace Hgabka\UtilsBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Form\FormBuilderInterface;

abstract class NestedTreeAdmin extends AbstractAdmin
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

    public function getSubFormBuilder(): FormBuilderInterface
    {
        $formBuilder = $this->getFormContractor()->getFormBuilder(
            $this->getUniqId() . 'sub',
            ['data_class' => $this->getClass(), ...$this->getFormOptions()],
        );

        $this->defineFormBuilder($formBuilder);

        return $formBuilder;
    }

    abstract public function getEntityName(string $locale): string;

    abstract public function getSubEntityName(string $locale): string;

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
