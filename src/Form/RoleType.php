<?php

namespace Hgabka\UtilsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * RoleType defines the form used for {@link Role}.
 */
class RoleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('role', TextType::class, [
                'required' => true,
                'label' => 'settings.role.role',
            ])
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'settings.role.name',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'role';
    }
}
