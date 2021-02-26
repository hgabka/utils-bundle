<?php

namespace Hgabka\UtilsBundle\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Hgabka\UtilsBundle\Doctrine\Hydrator\KeyValueHydrator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

class DynamicCollectionType extends AbstractType
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /**
     * DynamicCollectionType constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', TextType::class, [
                'label' => false,
            ])
            ->add('elements', FormType::class)
            ->addModelTransformer(new CallbackTransformer(
                function ($value) {
                    dump($value);
                    return null;
                },
                function ($data) {
                    dump('trans', $data);
                    return $data['elements'] ?? new ArrayCollection();
                }
            ))
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $data = $event->getData();
                $form = $event->getForm();

                if (null === $data) {
                    $data = [];
                }

                foreach ($data as $key => $entity) {
                    $form->get('elements')->add($key, DynamicElementType::class, [
                        'data' => [
                            'id' => $entity->getId(),
                            'name' => $entity->getName(),
                        ],
                    ])
                    ;
                }
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();
                if (null === $data || '' === $data) {
                    $data = ['elements' => []];
                }

                if (empty($data['elements'])) {
                    $data['elements'] = [];
                }

                // Add all additional rows
                foreach ($data['elements'] as $name => $value) {
                    if (!$form->get('elements')->has((string) $name)) {
                        $form->get('elements')->add($name, DynamicElementType::class, [
                            'data' => [
                                'id' => $value['id'] ?? '',
                                'name' => $value['name'],
                            ],
                        ])
                        ;
                    }
                }
            })
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();

                /** @var PersistentCollection $formData */
                $formData = $form->getData();
                dump($data);
                if (null === $data) {
                    $data = [];
                }

                if (!empty($formData)) {
                    foreach ($formData as $key => $child) {
                        if (empty($data['elements'][$key]) || null === $data['elements'][$key]['id']) {
                            unset($formData[$key]);
                        }

                        unset($data['elements'][$key]);
                    }
                }

                if (!empty($data['elements'])) {
                    $class = $form->getConfig()->getOption('class');
                    $repo = $this->entityManager->getRepository($class);
                    $property = $form->getConfig()->getOption('property');

                    foreach ($data['elements'] as $elementData) {
                        $entity = empty($elementData['id'])
                            ? null
                            : $repo->find($elementData['id']);

                        if (empty($entity) && empty($elementData['id'])) {
                            $entity = $repo->findOneBy([$property => trim($elementData['name'])]);
                        }

                        if (empty($entity)) {
                            $entity = new $class();
                            $propertyAccessor = PropertyAccess::createPropertyAccessor();
                            $propertyAccessor->setValue($entity, $property, trim($elementData['name']));

                            $this->entityManager->persist($entity);
                            $this->entityManager->flush($entity);
                        }

                        if (empty($formData)) {
                            $formData = new ArrayCollection();
                        }

                        if (!$formData->contains($entity)) {
                            $formData->add($entity);
                        }
                    }
                }
                $event->setData(['elements' => $formData]);
            })
        ;

        if ($options['allow_add'] && $options['prototype']) {
            $prototypeOptions = array_replace([
                'required' => $options['required'],
                'label' => $options['prototype_name'] . 'label__',
            ]);

            if (null !== $options['prototype_data']) {
                $prototypeOptions['data'] = $options['prototype_data'];
            } else {
                $prototypeOptions['data'] = [
                    'id' => '__entityid__',
                    'name' => '__entityname__',
                ];
            }

            $prototype = $builder->get('elements')->create($options['prototype_name'], DynamicElementType::class, $prototypeOptions);

            $builder->setAttribute('prototype', $prototype->getForm());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, [
            'allow_add' => $options['allow_add'],
        ]);

        if ($form->getConfig()->hasAttribute('prototype')) {
            $prototype = $form->getConfig()->getAttribute('prototype');
            $view->vars['prototype'] = $prototype->createView($form->get('elements')->createView($view));
        }

        $entities =
            $this
                ->entityManager
                ->getRepository($options['class'])
                ->createQueryBuilder('e')
                ->select('e.id')
                ->addSelect('e.' . $options['property'])
                ->getQuery()
                ->getResult(KeyValueHydrator::HYDRATOR_NAME)
        ;
        $results = [];
        foreach ($entities as $id => $name) {
            $results[] = ['id' => $id, 'name' => $name];
        }

        $view->vars['entities'] = $results;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => null,
            'property' => 'name',
            'allow_add' => true,
            'prototype' => true,
            'prototype_data' => null,
            'prototype_name' => '__name__',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'dynamic_collection';
    }
}
