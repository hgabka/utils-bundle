<?php

namespace Hgabka\UtilsBundle\Twig;

use Hgabka\UtilsBundle\Helper\AdminPanel\AdminPanel;
use Hgabka\UtilsBundle\Helper\AdminPanel\AdminPanelActionInterface;
use Hgabka\UtilsBundle\Helper\Menu\MenuBuilder;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MenuTwigExtension extends AbstractExtension
{
    public function __construct(protected readonly MenuBuilder $menuBuilder, protected readonly AdminPanel $adminPanel)
    {
    }

    /**
     * Get Twig functions defined in this extension.
     *
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_admin_menu', $this->getAdminMenu(...)),
            new TwigFunction('get_admin_panel_actions', $this->getAdminPanelActions(...)),
        ];
    }

    public function getAdminMenu(): ?MenuBuilder
    {
        return $this->menuBuilder;
    }

    public function getAdminPanelActions()
    {
        return $this->adminPanel->getAdminPanelActions();
    }
}
