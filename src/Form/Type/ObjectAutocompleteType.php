<?php

namespace Hgabka\UtilsBundle\Form\Type;

use Doctrine\Common\Persistence\AbstractManagerRegistry;
use Hgabka\UtilsBundle\Form\Transformer\ObjectAutocompleteViewTransformer;
use Symfony\Bridge\Doctrine\Form\EventListener\MergeDoctrineCollectionListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObjectAutocompleteType extends AbstractType
{
    /**
     * @var AbstractManagerRegistry
     */
    private $registry;

    public function __construct(AbstractManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $repo = $this->registry->getManager()->getRepository($options['class']);
        $builder
            ->addEventSubscriber(new MergeDoctrineCollectionListener())
            ->addViewTransformer(
                new ObjectAutocompleteViewTransformer(
                $repo,
                $options['to_string_callback']
            ),
                true
            )
        ;

        $builder->add('title', TextType::class, ['attr' => $options['attr']]);
        $builder->add('items', CollectionType::class, [
            'entry_type' => ObjectAutocompleteItemType::class,
            'entry_options' => [
                'repository' => $repo,
                'to_string_callback' => $options['to_string_callback'],
            ],
            'allow_add' => true,
            'allow_delete' => true,
            'attr' => array_merge($options['attr'], [
                'data-maximum-items' => $options['maximum_items'],
            ]),
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['placeholder'] = $options['placeholder'];
        $view->vars['minimum_input_length'] = $options['minimum_input_length'];
        $view->vars['maximum_items'] = $options['maximum_items'];

        // ajax parameters
        $view->vars['url'] = $options['url'];
        $view->vars['route'] = $options['route'];
        $view->vars['source'] = $options['source'];
        $view->vars['kumaPagePartEvents'] = $options['kuma_pagepart_events'];
    }

    public function getParent()
    {
        return FormType::class;
    }

    public function getBlockPrefix()
    {
        return 'object_autocomplete';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr' => [],
            'compound' => true,
            'placeholder' => '',
            'minimum_input_length' => 0,
            'maximum_items' => null,
            'to_string_callback' => null,
            'source' => [],
            'kuma_pagepart_events' => [],
            'url' => '',
            'route' => [
                'name' => '',
                'parameters' => [],
            ],
        ]);

        $resolver->setRequired(['class']);
    }
}
