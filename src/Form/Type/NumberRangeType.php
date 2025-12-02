<?php

namespace Hgabka\UtilsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NumberRangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('start', $options['field_type'], array_merge(['required' => false], $options['field_options']));
        $builder->add('end', $options['field_type'], array_merge(['required' => false], $options['field_options']));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'field_options'    => [],
            'field_type'       => TextType::class,
            'label_placement' => 'after',
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['labelPlacement'] = $options['label_placement'];
    }

    public function getBlockPrefix(): string
    {
        return 'number_range';
    }
}
