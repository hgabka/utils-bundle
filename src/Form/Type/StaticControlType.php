<?php

namespace Hgabka\UtilsBundle\Form\Type;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StaticControlType extends AbstractType
{
    /** @var EngineInterface */
    protected $templating;

    /**
     * StaticControlType constructor.
     *
     * @param TemplateRegistryInterface $twig
     */
    public function __construct(EngineInterface $templating)
    {
        $this->templating = $templating;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
          'required' => false,
          'disabled' => true,
          'html' => false,
          'template' => false,
          'template_vars' => [],
          'format' => '%s',
          'date_format' => null,
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['is_html'] = !empty($options['html']) || !empty($options['template']);
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $isDate = false;
        $value = \is_string($view->vars['value']) || \is_object($view->vars['value']) ? (string)$view->vars['value'] : '';
        if (!empty($options['template'])) {
            $val = $this->templating->render($options['template'], array_merge([
                'value' => $value,
                'options' => $options,
            ], $options['template_vars']));
        } else {
            $val = !empty($options['html']) ? str_replace('%value%', $value, $options['html']) : $value;
        }
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
