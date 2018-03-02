<?php

namespace Hgabka\UtilsBundle\Twig;

use Hgabka\UtilsBundle\Helper\FormHelper;
use Symfony\Component\Form\FormView;

/**
 * FormToolsExtension.
 */
class FormToolsExtension extends \Twig_Extension
{
    /**
     * @var FormHelper
     */
    private $formHelper;

    /**
     * @param FormHelper $formHelper
     */
    public function __construct(FormHelper $formHelper)
    {
        $this->formHelper = $formHelper;
    }

    /**
     * Get Twig functions defined in this extension.
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('form_errors_recursive', [$this, 'getErrorMessages']),
            new \Twig_SimpleFunction('form_has_errors_recursive', [$this, 'hasErrorMessages']),
        ];
    }

    /**
     * Return if there are error messages.
     *
     * @param FormView $formView
     *
     * @return bool
     */
    public function hasErrorMessages(FormView $formView)
    {
        return $this->formHelper->hasRecursiveErrorMessages($formView);
    }

    /**
     * Get the error messages.
     *
     * @param FormView|FormView[] $formViews The form views
     * @param array               &$errors   The errors
     *
     * @return array
     */
    public function getErrorMessages($formViews, array &$errors = [])
    {
        return $this->formHelper->getRecursiveErrorMessages($formViews, $errors);
    }
}
