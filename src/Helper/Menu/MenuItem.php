<?php

namespace Hgabka\UtilsBundle\Helper\Menu;

/**
 * A MenuItem is part of the menu in the admin interface, this will be build by the {@link MenuBuilder}.
 */
class MenuItem
{
    /**
     * @var MenuBuilder
     */
    private $menu;

    /**
     * @var string
     */
    private $uniqueId;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $role;

    /**
     * @var MenuItem
     */
    private $parent;

    /**
     * @var string
     */
    private $route;

    /**
     * @var array
     */
    private $routeParams = [];

    /**
     * @var bool
     */
    private $active = false;

    /**
     * @var bool
     */
    private $offline = false;

    /**
     * @var bool
     */
    private $folder = false;

    /**
     * @var MenuItem[]
     */
    private $children;

    /**
     * @var array
     */
    private $attributes = [];

    /**
     * @var bool
     */
    private $appearInNavigation = true;

    /**
     * @var int
     */
    private $weight = -50;

    /**
     * Construct the MenuItem.
     */
    public function __construct(MenuBuilder $menu)
    {
        $this->menu = $menu;
    }

    /**
     * Get menu builder.
     *
     * @return MenuBuilder
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * Get unique Id.
     *
     * @return string
     */
    public function getUniqueId()
    {
        return $this->uniqueId;
    }

    /**
     * Set unique id.
     *
     * @param string $uniqueId
     *
     * @return MenuItem
     */
    public function setUniqueId($uniqueId)
    {
        $this->uniqueId = $uniqueId;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return MenuItem
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get role.
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set role.
     *
     * @param string $role
     *
     * @return MenuItem
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get parent menu item.
     *
     * @return null|MenuItem
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set parent menu item.
     *
     * @param null|MenuItem $parent
     *
     * @return MenuItem
     */
    public function setParent(?self $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get route for menu item.
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set route and parameters for menu item.
     *
     * @param string $route  The route
     * @param array  $params The route parameters
     *
     * @return MenuItem
     */
    public function setRoute($route, array $params = [])
    {
        $this->route = $route;
        $this->routeParams = $params;

        return $this;
    }

    /**
     * Get route parameters for menu item.
     *
     * @return array
     */
    public function getRouteParams()
    {
        return $this->routeParams;
    }

    /**
     * Set route parameters.
     *
     * @return MenuItem
     */
    public function setRouteParams(array $routeParams = [])
    {
        $this->routeParams = $routeParams;

        return $this;
    }

    /**
     * Get children of current menu item.
     *
     * @return MenuItem[]
     */
    public function getChildren()
    {
        if (null === $this->children) {
            $this->children = $this->menu->getChildren($this);
        }

        return $this->children;
    }

    /**
     * Warning: the adaptChildren method on the menuadaptors will not be called anymore for this menuitem.
     *
     * @return MenuItem
     */
    public function setChildren(array $children = [])
    {
        $this->children = $children;

        return $this;
    }

    /**
     * Get children of current menu item that have the appearInNavigation flag set.
     *
     * @return MenuItem[]
     */
    public function getNavigationChildren()
    {
        $result = [];
        $children = $this->getChildren();
        foreach ($children as $child) {
            if ($child->getAppearInNavigation()) {
                $result[] = $child;
            }
        }

        return $result;
    }

    /**
     * Return top children of current menu item.
     *
     * @return TopMenuItem[]
     */
    public function getTopChildren()
    {
        $result = [];
        $children = $this->getChildren();
        foreach ($children as $child) {
            if ($child instanceof TopMenuItem) {
                $result[] = $child;
            }
        }

        return $result;
    }

    /**
     * Add attributes.
     *
     * @param array $attributes
     *
     * @return MenuItem
     */
    public function addAttributes($attributes)
    {
        $this->attributes = array_merge($this->attributes, $attributes);

        return $this;
    }

    /**
     * Get attributes.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Get menu item active state.
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set menu item active state.
     *
     * @param bool $active
     *
     * @return MenuItem
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get menu item offline state.
     *
     * @return bool
     */
    public function getOffline()
    {
        return $this->offline;
    }

    /**
     * Set menu item offline state.
     *
     * @param bool $offline
     *
     * @return MenuItem
     */
    public function setOffline($offline)
    {
        $this->offline = $offline;

        return $this;
    }

    /**
     * Get menu item folder state.
     *
     * @return bool
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * Set menu item folder state.
     *
     * @param bool $folder
     *
     * @return MenuItem
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;

        return $this;
    }

    /**
     * Get appearInNavigation flag.
     *
     * @return bool
     */
    public function getAppearInNavigation()
    {
        return $this->appearInNavigation;
    }

    /**
     * Set appearInNavigation flag.
     *
     * @param bool $appearInNavigation
     *
     * @return MenuItem
     */
    public function setAppearInNavigation($appearInNavigation)
    {
        $this->appearInNavigation = $appearInNavigation;

        return $this;
    }

    /**
     * Get weight.
     *
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set weight.
     *
     * @param int $weight
     *
     * @return MenuItem
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }
}
