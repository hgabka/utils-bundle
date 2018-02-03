<?php

namespace Hgabka\UtilsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DatepickerRangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start', $options['input_class'], array_merge($options['common_options'], $options['start_options']))
            ->add('end', $options['input_class'], array_merge($options['common_options'], $options['end_options']))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'input_class' => DatepickerType::class,
            'common_options' => [],
            'start_options' => [],
            'end_options' => [],
        ]);
    }

    public function getBlockPrefix()
    {
        return 'datepicker_range';
    }
}
