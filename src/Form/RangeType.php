<?php

namespace Hgabka\UtilsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * HTML5 range type field.
 */
class RangeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['attr' => ['min' => 0, 'max' => 100, 'step' => 1]]);
    }

    /**
     * Get parent.
     *
     * @return string
     */
    public function getParent()
    {
        return IntegerType::class;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'range';
    }
}
