<?php

namespace Hgabka\UtilsBundle\Breadcrumb;

use Hgabka\UtilsBundle\Breadcrumb\BreadcrumbManager;

trait BreadcrumbTrait
{
    /** @var BreadcrumbManager */
    protected $breadcrumbManager;

    /**
     * @required
     *
     * @param BreadcrumbManager $breadcrumbManager
     */
    public function setBreadcrumbManager(BreadcrumbManager $breadcrumbManager)
    {
        $this->breadcrumbManager = $breadcrumbManager;
    }
}