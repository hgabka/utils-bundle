<?php

namespace Hgabka\UtilsBundle\Breadcrumb;

use Symfony\Component\Security\Core\User\UserInterface;

interface BreadcrumbInterface
{
    /**
     * Az aktuális elemhez tartozó breadcrumb.
     *
     * @return BreadCrumb|BreadCrumb[]|string
     */
    public function getBreadcrumb(UserInterface $user);
}
