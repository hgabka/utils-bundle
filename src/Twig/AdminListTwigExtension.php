<?php

namespace Hgabka\UtilsBundle\Twig;

use Hgabka\UtilsBundle\AdminList\AdminList;
use Hgabka\UtilsBundle\Service\ExportService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * AdminListTwigExtension.
 */
class AdminListTwigExtension extends AbstractExtension
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('adminlist_widget', [$this, 'renderWidget'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new TwigFunction('supported_export_extensions', [$this, 'getSupportedExtensions']),
        ];
    }

    /**
     * Renders the HTML for a given view.
     *
     * Example usage in Twig:
     *
     *     {{ form_widget(view) }}
     *
     * You can pass options during the call:
     *
     *     {{ form_widget(view, {'attr': {'class': 'foo'}}) }}
     *
     *     {{ form_widget(view, {'separator': '+++++'}) }}
     *
     * @param AdminList $view      The view to render
     * @param string    $basepath  The base path
     * @param array     $urlparams Additional url params
     * @param array     $addparams Add params
     *
     * @return string The html markup
     */
    public function renderWidget(\Twig_Environment $env, AdminList $view, $basepath, array $urlparams = [], array $addparams = [])
    {
        $template = $env->loadTemplate('HgabkaUtilsBundle:AdminListTwigExtension:widget.html.twig');

        $filterBuilder = $view->getFilterBuilder();

        return $template->render([
            'filter' => $filterBuilder,
            'basepath' => $basepath,
            'addparams' => $addparams,
            'extraparams' => $urlparams,
            'adminlist' => $view,
        ]);
    }

    public function getSupportedExtensions()
    {
        return ExportService::getSupportedExtensions();
    }
}
