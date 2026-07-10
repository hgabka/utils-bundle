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
    public function getParent(): ?string
    {
        return TextareaType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'config' => null,
            ])
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['customconfig'] = $options['config'] ?? null;
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'wysiwyg';
    }
}
