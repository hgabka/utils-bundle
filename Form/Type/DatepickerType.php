<?php

namespace HG\UtilsBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;

class DatepickerType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

        $resolver->setDefaults(array(
          'image' => false,
          'datepicker_format' => 'yy-mm-dd',
          'showOn' => 'both',
          'buttonImage' => '/bundles/hgutils/images/datepicker.gif',
          'buttonImageOnly' => true,
          'changeMonth' => false,
          'changeYear' => false,
          'maxDate' => null,
          'minDate' => null,
          'showAnim' => 'show',
          'numberOfMonths' => 1,
          'config' => '{}',
          'onSelect' => null,
          'onClose' => null,
          'showWeek' => false,
          'yearSuffix' => '<span class="ui-datepicker-year">.</span>',
          'showMonthAfterYear' => true,
          'widget' => 'single_text',
          'format' => 'yyyy-MM-dd',
          'attr' => array(
              'autocomplete' => 'off',
              'class' => 'datepicker',
          ),
            ));
    }
    
    
  public function getParent()
  {
      return 'date';
  }

  public function getName()
  {
      return 'datepicker';
  }
  
  public function finishView(FormView $view, FormInterface $form, array $options)
  {
    $view->vars['image'] = $options['image'];
    $view->vars['config'] = $options['config'];
    $view->vars['config'] = $options['config'];
    $view->vars['onSelect'] = $options['onSelect'];
    $view->vars['onClose'] = $options['onClose'];
    $view->vars['showMonthAfterYear'] = $options['showMonthAfterYear'];
    $view->vars['format'] = $options['datepicker_format'];
    $view->vars['showOn'] = $options['showOn'];
    $view->vars['buttonImage'] = $options['buttonImage'];
    $view->vars['buttonImageOnly'] = $options['buttonImageOnly'];
    $view->vars['changeMonth'] = $options['changeMonth'];
    $view->vars['changeYear'] = $options['changeYear'];
    $view->vars['maxDate'] = $options['maxDate'];
    $view->vars['minDate'] = $options['minDate'];
    $view->vars['numberOfMonths'] = $options['numberOfMonths'];
    $view->vars['showAnim'] = $options['showAnim'];
    $view->vars['showWeek'] = $options['showWeek'];
    $view->vars['yearSuffix'] = $options['yearSuffix'];
    $view->vars['multi'] = $options['widget'] != 'single_text';
    
  }
}
