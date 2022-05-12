<?php

namespace Hgabka\UtilsBundle\Twig;

use Hgabka\UtilsBundle\Helper\Security\Acl\Permission\PermissionAdmin;
use Symfony\Component\Form\FormView;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * AdminPermissionsTwigExtension.
 */
class AdminPermissionsTwigExtension extends AbstractExtension
{
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('permissionsadmin_widget', [$this, 'renderWidget'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    /**
     * Renders the permission admin widget.
     *
     * @param PermissionAdmin $permissionAdmin The permission admin
     * @param FormView        $form            The form
     * @param array           $parameters      Extra parameters
     *
     * @return string
     */
    public function renderWidget(Environment $env, PermissionAdmin $permissionAdmin, FormView $form, array $parameters = [])
    {
        $template = $env->load('@HgabkaUtils/PermissionsAdminTwigExtension/widget.html.twig');

        return $template->render(array_merge([
            'form' => $form,
            'permissionadmin' => $permissionAdmin,
            'recursiveSupport' => true,
        ], $parameters));
    }
}
