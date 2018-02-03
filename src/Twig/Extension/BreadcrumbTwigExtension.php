<?php

namespace Hgabka\UtilsBundle\Twig\Extension;

use Hgabka\UtilsBundle\Breadcrumb\BreadcrumbManager;

class BreadcrumbTwigExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    /**
     * @var BreadcrumbManager
     */
    protected $breadcrumbManager;

    /**
     * PublicTwigExtension constructor.
     *
     * @param BreadcrumbManager $manager
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
