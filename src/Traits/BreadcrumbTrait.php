<?php

namespace Hgabka\UtilsBundle\Traits;

use Hgabka\UtilsBundle\Breadcrumb\BreadcrumbManager;

trait BreadcrumbTrait
{
    /** @var BreadcrumbManager */
    protected $breadcrumbManager;

    /**
     * @required
     */
    public function setBreadcrumbManager(BreadcrumbManager $breadcrumbManager)
    {
        $this->breadcrumbManager = $breadcrumbManager;
    }
}
