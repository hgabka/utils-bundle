<?php

namespace Hgabka\UtilsBundle\Breadcrumb;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class BreadcrumbManager implements \IteratorAggregate, \Countable
{
    /** @var array */
    protected $breadCrumbs = [];

    /** @var TokenStorageInterface */
    protected $tokenStorage;

    /** @var RequestStack */
    protected $requestStack;

    /** @var bool */
    protected $addHomepage = true;

    /** @var array */
    protected $predefinedLabels = [];

    private $waitingForLabel;

    /**
     * BreadcrumbManager constructor.
     *
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage, RequestStack $requestStack)
    {
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
    }

    /**
     * Visszaadja a breadcrumbs-okat.
     *
     * @return BreadCrumb[]
     */
    public function getBreadcrumbs()
    {
        if ($this->addHomepage && !empty($this->breadCrumbs)) {
            array_unshift($this->breadCrumbs, $this->getHomepageBreadcrumb());
            $this->addHomepage = false;
        }

        return $this->breadCrumbs;
    }

    /**
     * Egy új breadcrumb hozzáadása.
     *
     * @param BreadCrumb|BreadcrumbInterface|string $bc
     * @param string                                $label
     * @param mixed                                 $routeParams
     *
     * @return BreadcrumbManager
     */
    public function add($bc = null, $label = null, $routeParams = [])
    {
        if ($bc instanceof BreadcrumbInterface) {
            $bcs = $bc->getBreadcrumb($this->getUser());

            if (!\is_array($bcs)) {
                $bcs = [$bcs];
            }

            foreach (array_filter($bcs) as $b) {
                $this->add($b);
            }
        } elseif (null === $bc) {
            return $this->addCurrentRoute($label);
        } else {
            if (!$bc instanceof Breadcrumb) {
                $bc = new Breadcrumb($bc, $routeParams, $label);

                if (null === $label) {
                    $bc->setLabel($this->getPredefinedLabel($bc->getRoute()));
                }
            } elseif (null === $bc->getRoute()) {
                $request = $this->requestStack->getCurrentRequest();

                $bc->setRoute($request->attributes->get('_route'));
                $bc->setRouteParams($request->attributes->get('_route_params'));
            }

            $this->breadCrumbs[] = $bc;
        }

        return $this;
    }

    /**
     * Automatán megpróbálja kitalálni a route-ot.
     *
     * @param string $label
     *
     * @return BreadCrumbManager
     */
    public function addCurrentRoute($label = null)
    {
        $request = $this->requestStack->getCurrentRequest();
        $route = $request->attributes->get('_route');
        $routeParams = $request->attributes->get('_route_params');

        if (null === $label) {
            return $this->add($this->waitingForLabel = new Breadcrumb($route, $routeParams, $this->getPredefinedLabel($route)));
        }

        return $this->add($route, $label, $routeParams);
    }

    public function addHomePage()
    {
        return $this->setAddHomepage(false)->add($this->getHomepageBreadcrumb());
    }

    /**
     * Összes beállított breadcrumb törlése.
     */
    public function clear()
    {
        $this->breadCrumbs = [];

        return $this;
    }

    /**
     * Nyitólap breadcrumb.
     *
     * @return BreadCrumb
     */
    public function getHomepageBreadcrumb()
    {
        return new Breadcrumb('_slug', ['url' => '', '_locale' => $this->requestStack->getCurrentRequest()->getLocale()]);
    }

    /**
     * Beállítja, hogy hozzáadja-e a manager automatán a nyitólapot a breadcrumb lista elejére?
     *
     * @param bool $switch
     *
     * @return BreadCrumbManager
     */
    public function setAddHomepage($switch)
    {
        $this->addHomepage = (bool) $switch;

        return $this;
    }

    /**
     * Ha egy breadcrumbnak labelre van szükséges mert nem adtunk meg neki előtte, akkor ezzel be lehet állítani neki a megfelelő labelt.
     *
     * @param string $label
     *
     * @return BreadCrumbManager
     */
    public function setExternalLabel($label)
    {
        if (null !== $this->waitingForLabel) {
            $this->waitingForLabel->setLabel($label);
        }

        return $this;
    }

    /**
     * Törli az utolsó $count db breadcrumbot a lista végéről.
     *
     * @param int $count
     *
     * @return BreadCrumbManager
     */
    public function trim($count)
    {
        $this->breadCrumbs = \array_slice($this->getBreadcrumbs(), 0, -$count);

        return $this;
    }

    /**
     * Visszaadja a választott route-hoz az előre beállított labelt.
     *
     * @param string $route
     *
     * @return string
     */
    public function getPredefinedLabel($route)
    {
        $map = [
        ];

        return isset($map[$route]) ? $map[$route] : 'breadcrumb.'.$route;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->getBreadcrumbs());
    }

    public function count()
    {
        return \count($this->getBreadcrumbs());
    }

    public function isEmpty()
    {
        $bc = $this->getBreadcrumbs();

        return \count($bc) === ($this->addHomepage ? 0 : 1);
    }

    protected function getUser()
    {
        $user = $this->tokenStorage->getToken()->getUser();

        return \is_object($user) ? $user : null;
    }
}
