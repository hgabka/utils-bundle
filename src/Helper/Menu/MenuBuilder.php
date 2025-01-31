<?php

namespace Hgabka\UtilsBundle\Helper\Menu;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * The MenuBuilder will build the top menu and the side menu of the admin interface.
 */
class MenuBuilder
{
    /**
     * @var MenuAdaptorInterface[]
     */
    private $adaptors = [];

    /**
     * @var MenuAdaptorInterface[]
     */
    private $sorted = [];

    /**
     * @var TopMenuItem[]
     */
    private $topMenuItems;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var null|MenuItem
     */
    private $currentCache;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container The container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Add menu adaptor.
     *
     * @param mixed $priority
     */
    public function addAdaptMenu(MenuAdaptorInterface $adaptor, $priority = 0)
    {
        $this->adaptors[$priority][] = $adaptor;
        unset($this->sorted);
    }

    /**
     * Get current menu item.
     *
     * @return null|MenuItem
     */
    public function getCurrent()
    {
        if (null !== $this->currentCache) {
            return $this->currentCache;
        }
        // @var $active MenuItem
        $active = null;
        do {
            // @var MenuItem[] $children
            $children = $this->getChildren($active);
            $foundActiveChild = false;
            foreach ($children as $child) {
                if ($child->getActive()) {
                    $foundActiveChild = true;
                    $active = $child;

                    break;
                }
            }
        } while ($foundActiveChild);
        $this->currentCache = $active;

        return $active;
    }

    /**
     * Get breadcrumb path for current menu item.
     *
     * @return MenuItem[]
     */
    public function getBreadCrumb()
    {
        $result = [];
        $current = $this->getCurrent();
        while (null !== $current) {
            array_unshift($result, $current);
            $current = $current->getParent();
        }

        return $result;
    }

    /**
     * Get top parent menu of current menu item.
     *
     * @return null|TopMenuItem
     */
    public function getLowestTopChild()
    {
        $current = $this->getCurrent();
        while (null !== $current) {
            if ($current instanceof TopMenuItem) {
                return $current;
            }
            $current = $current->getParent();
        }

        return null;
    }

    /**
     * Get all top menu items.
     *
     * @return MenuItem[]
     */
    public function getTopChildren()
    {
        if (null === $this->topMenuItems) {
            // @var $request Request
            $request = $this->container->get('request_stack')->getCurrentRequest();
            $this->topMenuItems = [];
            foreach ($this->getAdaptors() as $menuAdaptor) {
                $menuAdaptor->adaptChildren($this, $this->topMenuItems, null, $request);
            }
        }

        return $this->topMenuItems;
    }

    /**
     * Get immediate children of the specified menu item.
     *
     * @param MenuItem $parent
     *
     * @return MenuItem[]
     */
    public function getChildren(?MenuItem $parent = null)
    {
        if (null === $parent) {
            return $this->getTopChildren();
        }
        // @var $request Request
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $result = [];
        foreach ($this->getAdaptors() as $menuAdaptor) {
            $menuAdaptor->adaptChildren($this, $result, $parent, $request);
        }

        return $result;
    }

    private function getAdaptors()
    {
        if (!isset($this->sorted)) {
            $this->sortAdaptors();
        }

        return $this->sorted;
    }

    /**
     * Sorts the internal list of adaptors by priority.
     */
    private function sortAdaptors()
    {
        $this->sorted = [];

        if (isset($this->adaptors)) {
            krsort($this->adaptors);
            $this->sorted = \call_user_func_array('array_merge', $this->adaptors);
        }
    }
}
