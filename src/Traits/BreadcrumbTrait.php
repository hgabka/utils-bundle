<?php

namespace Hgabka\UtilsBundle\Traits;

use Hgabka\UtilsBundle\Breadcrumb\BreadcrumbManager;
use Symfony\Contracts\Service\Attribute\Required;

trait BreadcrumbTrait
{
    /** @var BreadcrumbManager */
    protected ?BreadcrumbManager $breadcrumbManager = null;

    #[Required]
    public function setBreadcrumbManager(BreadcrumbManager $breadcrumbManager)
    {
        $this->breadcrumbManager = $breadcrumbManager;
    }
}
