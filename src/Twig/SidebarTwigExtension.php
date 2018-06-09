<?php

namespace Hgabka\UtilsBundle\Twig;

use Hgabka\NodeBundle\Entity\HideSidebarInNodeEditInterface;
use Hgabka\UtilsBundle\Helper\Menu\MenuBuilder;

class SidebarTwigExtension extends \Twig_Extension
{
    /**
     * Get Twig functions defined in this extension.
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('hideSidebarInNodeEditAdmin', [$this, 'hideSidebarInNodeEditAdmin']),
        ];
    }

    /**
     * Return the admin menu MenuBuilder.
     *
     * @param mixed $node
     *
     * @return MenuBuilder
     */
    public function hideSidebarInNodeEditAdmin($node)
    {
        return $node instanceof HideSidebarInNodeEditInterface;
    }
}
