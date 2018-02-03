<?php

namespace Hgabka\UtilsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StaticControlType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
          'required' => false,
          'disabled' => true,
          'html' => false,
          'format' => '%s',
          'date_format' => null,
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['is_html'] = !empty($options['html']);
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $isDate = false;
        $val = !empty($options['html']) ? str_replace('%value%', $view->vars['value'], $options['html']) : $view->vars['value'];
        if ($val instanceof \DateTime) {
            $isDate = true;
        } else {
            $val = sprintf($options['format'], $val);
        }

        $view->vars['is_date'] = $isDate;
        $view->vars['date_format'] = $options['date_format'];
        $view->vars['value'] = $val;
    }

    public function getParent()
    {
        return TextType::class;
    }

    public function getBlockPrefix()
    {
        return 'hgabka_plain';
    }
}
