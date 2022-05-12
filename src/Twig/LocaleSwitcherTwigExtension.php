<?php

namespace Hgabka\UtilsBundle\Twig;

use Hgabka\UtilsBundle\Helper\HgabkaUtils;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * LocaleSwitcherTwigExtension.
 */
class LocaleSwitcherTwigExtension extends AbstractExtension
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
    public function getFunctions(): array
    {
        return [
            new TwigFunction('localeswitcher_widget', [$this, 'renderWidget'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new TwigFunction('get_locales', [$this, 'getLocales']),
            new TwigFunction('get_backend_locales', [$this, 'getBackendLocales']),
            new TwigFunction('locale_display_name', [$this, 'getLocaleDisplayName']),
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
    public function renderWidget(Environment $env, $locales, $route, array $parameters = [])
    {
        $template = $env->load(
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
