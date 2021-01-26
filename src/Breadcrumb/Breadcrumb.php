<?php

namespace Hgabka\UtilsBundle\Breadcrumb;

class Breadcrumb
{
    const LABEL_PREFIX = 'breadcrumb.';
    const LABEL_SUFFIX = '';

    protected $route;
    protected $routeParams;
    protected $label;
    protected $i18nParams;
    protected $forceLink = false;

    public function __construct($route, $routeParams = [], $label = null, array $i18nParams = [])
    {
        $this->setRoute($route);
        $this->setRouteParams($routeParams);
        $this->setLabel($label);
        $this->setI18nParams($i18nParams);
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function setRoute($route)
    {
        $this->route = $route ?? null;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRouteParams()
    {
        return $this->routeParams;
    }

    /**
     * @param mixed $routeParams
     *
     * @return Breadcrumb
     */
    public function setRouteParams($routeParams)
    {
        $this->routeParams = $routeParams;

        return $this;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setLabel($label)
    {
        if (empty($label)) {
            $label = self::LABEL_PREFIX.$this->getRoute().self::LABEL_SUFFIX;
        }

        $this->label = $label;

        return $this;
    }

    /**
     * @return array
     */
    public function getI18nParams()
    {
        return $this->i18nParams;
    }

    /**
     * @return $this
     */
    public function setI18nParams(array $i18nParams)
    {
        $this->i18nParams = $i18nParams;

        return $this;
    }

    public function isForceLink(): bool
    {
        return $this->forceLink;
    }

    /**
     * @return Breadcrumb
     */
    public function setForceLink(bool $forceLink): self
    {
        $this->forceLink = $forceLink;

        return $this;
    }
}
