<?php

namespace Hgabka\UtilsBundle\Tests\Helper;

use Hgabka\UtilsBundle\Helper\Menu\MenuAdaptorInterface;
use Hgabka\UtilsBundle\Helper\Menu\MenuBuilder;
use Hgabka\UtilsBundle\Helper\Menu\MenuItem;
use Hgabka\UtilsBundle\Helper\Menu\SimpleMenuAdaptor;
use Hgabka\UtilsBundle\Helper\Menu\TopMenuItem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MenuTest extends TestCase
{
    private function builder(?Request $request = null): MenuBuilder
    {
        $stack = new RequestStack();
        if ($request) {
            $stack->push($request);
        }
        $container = new Container();
        $container->set('request_stack', $stack);

        return new MenuBuilder($container);
    }

    private function request(string $route): Request
    {
        $request = Request::create('/');
        $request->attributes->set('_route', $route);

        return $request;
    }

    private function simpleAdaptor(array $granted = []): SimpleMenuAdaptor
    {
        $checker = $this->createStub(AuthorizationCheckerInterface::class);
        $checker->method('isGranted')->willReturnCallback(static fn (string $role): bool => \in_array($role, $granted, true));

        return new SimpleMenuAdaptor($checker, [
            ['route' => 'dashboard', 'params' => [], 'label' => 'Dashboard', 'role' => null, 'parent' => null],
            ['route' => 'users', 'params' => [], 'label' => 'Users', 'role' => 'ROLE_ADMIN', 'parent' => null],
            ['route' => 'user_new', 'params' => ['a' => 1], 'label' => 'New user', 'role' => null, 'parent' => 'users'],
        ]);
    }

    public function testMenuItemAccessors(): void
    {
        $item = new MenuItem($this->builder());

        $result = $item->setLabel('Label')->setRoute('route', ['id' => 1])->setUniqueId('uid')->setRole('ROLE_X')->setWeight(5)->setActive(true)->setOffline(true)->setFolder(true)->setAppearInNavigation(false);

        $this->assertSame($item, $result);
        $this->assertSame(['Label', 'route', ['id' => 1], 'uid', 'ROLE_X', 5, true, true, true, false], [
            $item->getLabel(), $item->getRoute(), $item->getRouteParams(), $item->getUniqueId(), $item->getRole(), $item->getWeight(),
            $item->getActive(), $item->getOffline(), $item->getFolder(), $item->getAppearInNavigation(),
        ]);
    }

    public function testNavigationAndTopChildrenAreFiltered(): void
    {
        $builder = $this->builder();
        $parent = new MenuItem($builder);
        $hidden = (new MenuItem($builder))->setAppearInNavigation(false);
        $shown = new MenuItem($builder);
        $top = new TopMenuItem($builder);
        $parent->setChildren([$hidden, $shown, $top]);

        $this->assertSame([$shown, $top], $parent->getNavigationChildren());
        $this->assertSame([$top], $parent->getTopChildren());
    }

    public function testAdaptorsDecideWhichItemsAppear(): void
    {
        $builder = $this->builder();
        $builder->addAdaptMenu($this->simpleAdaptor());

        $labels = array_map(static fn (MenuItem $i) => $i->getLabel(), $builder->getTopChildren());

        $this->assertSame(['Dashboard'], $labels);
    }

    public function testRoleRestrictedItemsAppearForGrantedUsers(): void
    {
        $builder = $this->builder();
        $builder->addAdaptMenu($this->simpleAdaptor(['ROLE_ADMIN']));

        $top = $builder->getTopChildren();
        $this->assertSame(['Dashboard', 'Users'], array_map(static fn (MenuItem $i) => $i->getLabel(), $top));

        $children = $builder->getChildren($top[1]);
        $this->assertSame(['New user'], array_map(static fn (MenuItem $i) => $i->getLabel(), $children));
        $this->assertSame($top[1], $children[0]->getParent());
        $this->assertSame(['a' => 1], $children[0]->getRouteParams());
        $this->assertInstanceOf(TopMenuItem::class, $children[0]);
    }

    private function activeAdaptor(): MenuAdaptorInterface
    {
        return new class() implements MenuAdaptorInterface {
            public function adaptChildren(MenuBuilder $menu, array &$children, ?MenuItem $parent = null, ?Request $request = null)
            {
                if (null === $parent) {
                    $children[] = (new TopMenuItem($menu))->setLabel('Users')->setRoute('users')->setActive(true);
                    $children[] = (new TopMenuItem($menu))->setLabel('Other')->setRoute('other');
                } elseif ('users' === $parent->getRoute()) {
                    $children[] = (new MenuItem($menu))->setLabel('New user')->setRoute('user_new')->setParent($parent)->setActive(true);
                }
            }
        };
    }

    public function testActiveItemAndBreadcrumb(): void
    {
        $builder = $this->builder();
        $builder->addAdaptMenu($this->activeAdaptor());

        $this->assertSame('New user', $builder->getCurrent()->getLabel());
        $this->assertSame(['Users', 'New user'], array_map(static fn (MenuItem $i) => $i->getLabel(), $builder->getBreadCrumb()));
        $this->assertSame('Users', $builder->getLowestTopChild()->getLabel());
    }

    public function testNoActiveItemMeansNoCurrent(): void
    {
        $builder = $this->builder($this->request('somewhere_else'));
        $builder->addAdaptMenu($this->simpleAdaptor());

        $this->assertNull($builder->getCurrent());
        $this->assertSame([], $builder->getBreadCrumb());
        $this->assertNull($builder->getLowestTopChild());
    }

    public function testAdaptorsRunInPriorityOrder(): void
    {
        $order = [];
        $make = function (string $name) use (&$order): MenuAdaptorInterface {
            return new class($name, $order) implements MenuAdaptorInterface {
                public function __construct(private string $name, private array &$order)
                {
                }

                public function adaptChildren(MenuBuilder $menu, array &$children, ?MenuItem $parent = null, ?Request $request = null)
                {
                    $this->order[] = $this->name;
                }
            };
        };
        $builder = $this->builder();
        $builder->addAdaptMenu($make('low'), -5);
        $builder->addAdaptMenu($make('high'), 10);
        $builder->addAdaptMenu($make('default'));

        $builder->getTopChildren();

        $this->assertSame(['high', 'default', 'low'], $order);
    }

    public function testTopMenuItemSidebarFlag(): void
    {
        $item = new TopMenuItem($this->builder());

        $this->assertSame($item, $item->setAppearInSidebar(false));
        $this->assertFalse($item->getAppearInSidebar());
    }
}
