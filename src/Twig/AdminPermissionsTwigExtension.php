<?php

namespace Hgabka\UtilsBundle\Twig;

use Hgabka\UtilsBundle\Helper\Security\Acl\Permission\PermissionAdmin;
use Symfony\Component\Form\FormView;
use Twig_Environment;

/**
 * AdminPermissionsTwigExtension.
 */
class AdminPermissionsTwigExtension extends \Twig_Extension
{
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('permissionsadmin_widget', [$this, 'renderWidget'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    /**
     * Renders the permission admin widget.
     *
     * @param \Twig_Environment $env
     * @param PermissionAdmin   $permissionAdmin The permission admin
     * @param FormView          $form            The form
     * @param array             $parameters      Extra parameters
     *
     * @return string
     */
    public function renderWidget(Twig_Environment $env, PermissionAdmin $permissionAdmin, FormView $form, array $parameters = [])
    {
        $template = $env->loadTemplate('@HgabkaUtils/PermissionsAdminTwigExtension/widget.html.twig');

        return $template->render(array_merge([
            'form' => $form,
            'permissionadmin' => $permissionAdmin,
            'recursiveSupport' => true,
        ], $parameters));
    }
}
