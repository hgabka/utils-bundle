<?php

namespace Hgabka\UtilsBundle\Helper\FormWidgets\Tabs;

use Hgabka\UtilsBundle\Helper\FormWidgets\FormWidgetInterface;

/**
 * A tab can be added to the TabPane and show fields or other information of a certain entity.
 */
interface TabInterface extends FormWidgetInterface
{
    /**
     * @return string
     */
    public function getTitle();
}
