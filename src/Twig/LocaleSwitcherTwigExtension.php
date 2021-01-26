<?php

namespace Hgabka\UtilsBundle\Twig;

use Hgabka\UtilsBundle\Helper\HgabkaUtils;

/**
 * LocaleSwitcherTwigExtension.
 */
class LocaleSwitcherTwigExtension extends \Twig_Extension
{
    /**
     * @var HgabkaUtils
     */
    private $hgabkaUtils;

    public function __construct(HgabkaUtils $hgabkaUtils)
    {
        $this->hgabkaUtils = $hgabkaUtils;
    }

    /**
     * Get Twig functions defined in this extension.
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('localeswitcher_widget', [$this, 'renderWidget'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('get_locales', [$this, 'getLocales']),
            new \Twig_SimpleFunction('get_backend_locales', [$this, 'getBackendLocales']),
            new \Twig_SimpleFunction('locale_display_name', [$this, 'getLocaleDisplayName']),
        ];
    }

    /**
     * Render locale switcher widget.
     *
     * @param array  $locales    The locales
     * @param string $route      The route
     * @param array  $parameters The route parameters
     *
     * @return string
     */
    public function renderWidget(\Twig_Environment $env, $locales, $route, array $parameters = [])
    {
        $template = $env->loadTemplate(
            '@HgabkaUtils/LocaleSwitcherTwigExtension/widget.html.twig'
        );

        return $template->render(
            array_merge(
                $parameters,
                [
                    'locales' => $locales,
                    'route' => $route,
                ]
            )
        );
    }

    /**
     * @return array
     */
    public function getLocales()
    {
        return $this->hgabkaUtils->getAvailableLocales();
    }

    /**
     * @param null|mixed $switchedHost
     *
     * @return array
     */
    public function getBackendLocales()
    {
        return $this->hgabkaUtils->getAvailableLocales();
    }

    public function getLocaleDisplayName($culture, $locale = null)
    {
        return $this->hgabkaUtils->getIntlLocale($culture, $locale);
    }
}
