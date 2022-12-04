<?php

namespace Hgabka\UtilsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class DynamicElementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', HiddenType::class)
            ->add('name', HiddenType::class)
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'dynamic_element';
    }
}
