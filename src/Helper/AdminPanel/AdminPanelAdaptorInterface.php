<?php

namespace Hgabka\UtilsBundle\Helper\AdminPanel;

interface AdminPanelAdaptorInterface
{
    /**
     * @return AdminPanelActionInterface[]
     */
    public function getAdminPanelActions();
}
