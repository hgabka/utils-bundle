<?php

namespace Hgabka\UtilsBundle\Twig\Extension;

use Hgabka\UtilsBundle\Breadcrumb\BreadcrumbManager;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class BreadcrumbTwigExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(private readonly BreadcrumbManager $breadcrumbManager)
    {
    }

    public function getGlobals(): array
    {
        return ['breadcrumb_manager' => $this->breadcrumbManager];
    }
}
