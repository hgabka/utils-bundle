<?php

namespace Hgabka\UtilsBundle\Twig;

use Hgabka\UtilsBundle\Helper\AdminPanel\AdminPanel;
use Hgabka\UtilsBundle\Helper\AdminPanel\AdminPanelActionInterface;
use Hgabka\UtilsBundle\Helper\Menu\MenuBuilder;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MenuTwigExtension extends AbstractExtension
{
    /**
     * @var MenuBuilder
     */
    protected $menuBuilder;

    /**
     * @var AdminPanel
     */
    protected $adminPanel;

    public function __construct(MenuBuilder $menuBuilder, AdminPanel $adminPanel)
    {
        $this->menuBuilder = $menuBuilder;
        $this->adminPanel = $adminPanel;
    }

    /**
     * Get Twig functions defined in this extension.
     *
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_admin_menu', [$this, 'getAdminMenu']),
            new TwigFunction('get_admin_panel_actions', [$this, 'getAdminPanelActions']),
        ];
    }

    /**
     * Return the admin menu MenuBuilder.
     *
     * @return MenuBuilder
     */
    public function getAdminMenu(): ?MenuBuilder
    {
        return $this->menuBuilder;
    }

    /**
     * Return the admin panel actions.
     *
     * @return AdminPanelActionInterface[]
     */
    public function getAdminPanelActions()
    {
        return $this->adminPanel->getAdminPanelActions();
    }
}
