<?php

namespace Hgabka\UtilsBundle\Helper;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class DomainConfiguration.
 *
 * Default (single domain) configuration handling
 */
class DomainConfiguration implements DomainConfigurationInterface
{
    /** @var ContainerInterface */
    protected $container;

    /** @var bool */
    protected $multiLanguage;

    /** @var array */
    protected $requiredLocales;

    /** @var string */
    protected $defaultLocale;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->multiLanguage = $this->container->getParameter(
            'multilanguage'
        );
        $this->defaultLocale = $this->container->getParameter(
            'defaultlocale'
        );
        $this->requiredLocales = explode(
            '|',
            $this->container->getParameter('requiredlocales')
        );
    }

    /**
     * @return string
     */
    public function getHost()
    {
        $request = $this->getMasterRequest();
        $host = null === $request ? '' : $request->getHost();

        return $host;
    }

    /**
     * @return array
     */
    public function getHosts()
    {
        return [$this->getHost()];
    }

    /**
     * @return string
     */
    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }

    /**
     * @param null|string $host
     *
     * @return bool
     */
    public function isMultiLanguage($host = null)
    {
        return $this->multiLanguage;
    }

    /**
     * @param null|string $host
     *
     * @return array
     */
    public function getFrontendLocales($host = null)
    {
        return $this->requiredLocales;
    }

    /**
     * @param null|string $host
     *
     * @return array
     */
    public function getBackendLocales($host = null)
    {
        return $this->requiredLocales;
    }

    /**
     * @return bool
     */
    public function isMultiDomainHost()
    {
        return false;
    }

    /**
     * @param null|string $host
     */
    public function getRootNode($host = null)
    {
        return null;
    }

    /**
     * @return array
     */
    public function getExtraData()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getLocalesExtraData()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getFullHostConfig()
    {
        return [];
    }

    /**
     * @param null|string $host
     */
    public function getFullHost($host = null)
    {
        return null;
    }

    /**
     * @param int $id
     */
    public function getFullHostById($id)
    {
        return null;
    }

    public function getHostSwitched()
    {
        return null;
    }

    /**
     * @param null|string $host
     */
    public function getHostBaseUrl($host = null)
    {
        return null;
    }

    /**
     * @return null|\Symfony\Component\HttpFoundation\Request
     */
    protected function getMasterRequest()
    {
        /** @var RequestStack $requestStack */
        $requestStack = $this->container->get('request_stack');

        return $requestStack->getMasterRequest();
    }
}
