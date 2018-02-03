<?php

namespace Hgabka\UtilsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DatepickerType extends AbstractType
{
    protected $jsOpts = [
        'format' => 'YYYY-MM-DD',
        'locale' => 'hu',
        'use_button' => true,
        'js-options' => [],
    ];

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new CallbackTransformer(
            function ($transform) {
                if (null !== $transform) {
                    /** @var $transform \DateTime */
                    $transform = $transform->format('Y-m-d');
                }

                return $transform;
            },
            function ($reverse) {
                if (null !== $reverse) {
                    $reverse = new \DateTime($reverse);
                }

                return $reverse;
            }
        ));

        $builder->setAttribute('data-options', json_encode($options['js-options']));
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        foreach (array_keys($this->jsOpts) as $optName) {
            $view->vars[$optName] = $options[$optName];
        }

        if (!isset($view->vars['attr']['data-options'])) {
            $view->vars['attr']['data-options'] = json_encode($options['js-options']);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults($this->jsOpts);
    }

    public function getParent()
    {
        return TextType::class;
    }

    public function getBlockPrefix()
    {
        return 'hgabka_datepicker';
    }
}
