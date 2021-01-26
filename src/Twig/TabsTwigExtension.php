<?php

namespace Hgabka\UtilsBundle\Twig;

use Hgabka\UtilsBundle\Helper\FormWidgets\Tabs\TabPane;
use Twig_Environment;
use Twig_Extension;

/**
 * Extension to render tabs.
 */
class TabsTwigExtension extends Twig_Extension
{
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('tabs_widget', [$this, 'renderWidget'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    /**
     * @param TabPane $tabPane  The tab pane
     * @param array   $options  The extra options
     * @param string  $template The template
     *
     * @return string
     */
    public function renderWidget(Twig_Environment $env, TabPane $tabPane, $options = [], $template = '@HgabkaUtils/TabsTwigExtension/widget.html.twig')
    {
        $template = $env->loadTemplate($template);

        return $template->render(array_merge($options, [
            'tabPane' => $tabPane,
        ]));
    }
}
