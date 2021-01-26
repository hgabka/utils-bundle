<?php

namespace Hgabka\UtilsBundle\Form\Type;

use Hgabka\UtilsBundle\Form\Transformer\ObjectAutocompleteItemViewTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObjectAutocompleteItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addViewTransformer(
                new ObjectAutocompleteItemViewTransformer(
                    $options['repository'],
                    $options['to_string_callback']
                ),
                true
            )
        ;
    }

    public function getParent()
    {
        return HiddenType::class;
    }

    public function getBlockPrefix()
    {
        return 'object_autocomplete_item';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'repository' => null,
            'to_string_callback' => null,
        ]);

        $resolver->setRequired(['repository']);
    }
}
