<?php

namespace Hgabka\UtilsBundle\Form\Type;

use Hgabka\UtilsBundle\Form\Transformer\ObjectAutocompleteItemViewTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObjectAutocompleteItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
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

    public function getParent(): ?string
    {
        return HiddenType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'object_autocomplete_item';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'repository' => null,
            'to_string_callback' => null,
        ]);

        $resolver->setRequired(['repository']);
    }
}
