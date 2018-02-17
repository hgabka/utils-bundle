<?php

namespace Hgabka\UtilsBundle\Breadcrumb;

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
