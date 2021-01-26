<?php

namespace Hgabka\UtilsBundle\Twig\Extension;

use Hgabka\UtilsBundle\Breadcrumb\BreadcrumbManager;
use Twig\Extension\GlobalsInterface;

class BreadcrumbTwigExtension extends \Twig_Extension implements GlobalsInterface
{
    /**
     * @var BreadcrumbManager
     */
    protected $breadcrumbManager;

    /**
     * PublicTwigExtension constructor.
     */
    public function __construct(BreadcrumbManager $manager)
    {
        $this->breadcrumbManager = $manager;
    }

    public function getGlobals()
    {
        return ['breadcrumb_manager' => $this->breadcrumbManager];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'hgabka_utilsbundle_breadcrumb_twig_extension';
    }
}
