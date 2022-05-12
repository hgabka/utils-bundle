<?php

namespace Hgabka\UtilsBundle\Twig;

use Hgabka\NodeBundle\Entity\HideSidebarInNodeEditInterface;
use Hgabka\UtilsBundle\Helper\Menu\MenuBuilder;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SidebarTwigExtension extends AbstractExtension
{
    /**
     * Get Twig functions defined in this extension.
     *
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('hideSidebarInNodeEditAdmin', [$this, 'hideSidebarInNodeEditAdmin']),
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
