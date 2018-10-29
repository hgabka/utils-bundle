<?php

namespace Hgabka\UtilsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class WysiwygType.
 */
class WysiwygType extends AbstractType
{
    /**
     * @return string
     */
    public function getParent()
    {
        return TextareaType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'contents_css' => null,
                'extra_allowed_content' => null,
            ])
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['contentscss'] = empty($options['contents_css'])
            ? null
            : (\is_string($options['contents_css']) ? [$options['contents_css']] : $options['contents_css'])
        ;
        $view->vars['extraallowedcontent'] = $options['extra_allowed_content'] ?? null;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'wysiwyg';
    }
}
