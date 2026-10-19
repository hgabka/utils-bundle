<?php

namespace Hgabka\UtilsBundle\Tests\Breadcrumb;

use Hgabka\UtilsBundle\Breadcrumb\Breadcrumb;
use Hgabka\UtilsBundle\Breadcrumb\BreadcrumbInterface;
use Hgabka\UtilsBundle\Breadcrumb\BreadcrumbManager;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class BreadcrumbTest extends TestCase
{
    private function manager(?string $route = 'product_show', array $routeParams = ['id' => 3], string $locale = 'hu', $user = null): BreadcrumbManager
    {
        $request = Request::create('/');
        $request->setLocale($locale);
        $request->attributes->set('_route', $route);
        $request->attributes->set('_route_params', $routeParams);
        $stack = new RequestStack();
        $stack->push($request);

        $security = $this->createStub(Security::class);
        $security->method('getUser')->willReturn($user);

        return new BreadcrumbManager($security, $stack);
    }

    public function testBreadcrumbLabelDefaultsToThePrefixedRoute(): void
    {
        $breadcrumb = new Breadcrumb('product_show', ['id' => 1]);

        $this->assertSame('breadcrumb.product_show', $breadcrumb->getLabel());
        $this->assertSame(['id' => 1], $breadcrumb->getRouteParams());
        $this->assertSame([], $breadcrumb->getI18nParams());
        $this->assertFalse($breadcrumb->isForceLink());
    }

    public function testBreadcrumbAccessors(): void
    {
        $breadcrumb = (new Breadcrumb('a'))->setRoute('b')->setLabel('Label')->setRouteParams(['x' => 1])->setI18nParams(['%n%' => 2])->setForceLink(true);

        $this->assertSame(['b', 'Label', ['x' => 1], ['%n%' => 2], true], [$breadcrumb->getRoute(), $breadcrumb->getLabel(), $breadcrumb->getRouteParams(), $breadcrumb->getI18nParams(), $breadcrumb->isForceLink()]);
        $this->assertSame('breadcrumb.b', $breadcrumb->setLabel('')->getLabel());
    }

    public function testAddByRouteName(): void
    {
        $manager = $this->manager();

        $this->assertSame($manager, $manager->add('home', 'Főoldal'));
        $manager->add('category', null, ['slug' => 'x']);

        $breadcrumbs = $manager->setAddHomepage(false)->getBreadcrumbs();
        $this->assertSame(['Főoldal', 'breadcrumb.category'], array_map(static fn (Breadcrumb $b) => $b->getLabel(), $breadcrumbs));
        $this->assertSame(['slug' => 'x'], $breadcrumbs[1]->getRouteParams());
    }

    public function testBreadcrumbWithoutRouteTakesTheCurrentOne(): void
    {
        $manager = $this->manager('cart', ['step' => 2]);

        $manager->add(new Breadcrumb(null, [], 'Cart'));

        [$breadcrumb] = $manager->setAddHomepage(false)->getBreadcrumbs();
        $this->assertSame(['cart', ['step' => 2], 'Cart'], [$breadcrumb->getRoute(), $breadcrumb->getRouteParams(), $breadcrumb->getLabel()]);
    }

    public function testAddWithoutArgumentsAddsTheCurrentRoute(): void
    {
        $manager = $this->manager('cart', ['step' => 2]);

        $manager->add();

        [$breadcrumb] = $manager->setAddHomepage(false)->getBreadcrumbs();
        $this->assertSame(['cart', 'breadcrumb.cart'], [$breadcrumb->getRoute(), $breadcrumb->getLabel()]);
    }

    public function testCurrentRouteWithLabel(): void
    {
        $manager = $this->manager('cart');

        $manager->addCurrentRoute('Kosár');

        $this->assertSame('Kosár', $manager->setAddHomepage(false)->getBreadcrumbs()[0]->getLabel());
    }

    public function testBreadcrumbProvidersAreExpanded(): void
    {
        $provider = new class() implements BreadcrumbInterface {
            public function getBreadcrumb($user = null)
            {
                return [new Breadcrumb('a', [], 'A'), null, new Breadcrumb('b', [], 'B')];
            }
        };
        $manager = $this->manager();

        $manager->add($provider);

        $this->assertSame(['A', 'B'], array_map(static fn (Breadcrumb $b) => $b->getLabel(), $manager->setAddHomepage(false)->getBreadcrumbs()));
    }

    public function testHomepageIsPrependedOnceWhenThereAreBreadcrumbs(): void
    {
        $manager = $this->manager('x', [], 'en');
        $this->assertSame([], $manager->getBreadcrumbs());

        $manager->add('page', 'Page');
        $breadcrumbs = $manager->getBreadcrumbs();

        $this->assertCount(2, $breadcrumbs);
        $this->assertSame(['_slug', ['url' => '', '_locale' => 'en']], [$breadcrumbs[0]->getRoute(), $breadcrumbs[0]->getRouteParams()]);
        $this->assertCount(2, $manager->getBreadcrumbs(), 'the homepage must not be added twice');
    }

    public function testAddHomePageAddsItExplicitly(): void
    {
        $manager = $this->manager();

        $manager->addHomePage()->add('page', 'Page');

        $this->assertCount(2, $manager->getBreadcrumbs());
    }

    public function testClearTrimCountAndIteration(): void
    {
        $manager = $this->manager()->setAddHomepage(false);
        $manager->add('a', 'A')->add('b', 'B')->add('c', 'C');

        $this->assertCount(3, $manager);
        $this->assertSame(['A', 'B', 'C'], array_map(static fn (Breadcrumb $b) => $b->getLabel(), iterator_to_array($manager)));

        $manager->trim(1);
        $this->assertCount(2, $manager);

        $manager->clear();
        $this->assertCount(0, $manager);
    }

    public function testIsEmpty(): void
    {
        $manager = $this->manager()->setAddHomepage(false);
        $this->assertFalse($manager->isEmpty(), 'with the homepage disabled a single entry counts as empty only when it is the lone breadcrumb');

        $manager->add('a', 'A');
        $this->assertTrue($manager->isEmpty());

        $manager->add('b', 'B');
        $this->assertFalse($manager->isEmpty());
    }

    public function testExternalLabelIsAppliedToTheCurrentRouteBreadcrumb(): void
    {
        $manager = $this->manager('cart')->setAddHomepage(false);
        $manager->addCurrentRoute();

        $this->assertSame($manager, $manager->setExternalLabel('Kosár'));
        $this->assertSame('Kosár', $manager->getBreadcrumbs()[0]->getLabel());
    }

    public function testPredefinedLabel(): void
    {
        $this->assertSame('breadcrumb.some_route', $this->manager()->getPredefinedLabel('some_route'));
    }
}
