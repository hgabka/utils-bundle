<?php

namespace HG\UtilsBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;

class UploadifyType extends AbstractType
{
  private $paramName;
  private $router;


  public function setParamName($paramName)
  {
    $this->paramName = $paramName;
  }

  public function setRouter($router)
  {
    $this->router = $router;
  }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

        $resolver->setDefaults(array(
            'route' => 'hg_utils_uploadify',
            'route_params' => array(),
            'size' => 'null',
            'js_upload_complete_callback' => '',
            'render_controller' => 'HGUtilsBundle:Default:uploadifyRender',
            'file_types' => '*.*',
            'html' => null,
            'btn_label' => 'btn_widget_upload',
            'debug' => false,
            'upload_form_type' => 'uploadify_upload'
            ));
    }


  public function getParent()
  {
      return 'file';
  }

  public function getBlockPrefix()
  {
      return 'uploadify';
  }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
      if (empty($options['render_controller']))
      {
        throw new InvalidArgumentException('Ervenytelen controller');
      }

      $view->vars['url'] = $this->router->generate($options['route'],
         array_merge($options['route_params'], array(
           'name' => $form->getName(),
           'formType' => $options['upload_form_type'],
           'controller' => empty($options['render_controller']) ? 'null' : $options['render_controller'],
           )));

      $view->vars['size'] = $options['size'];
      $view->vars['has_callback'] = !empty($options['js_upload_complete_callback']) ? 1 : 0;
      $view->vars['js_upload_callback'] = $options['js_upload_complete_callback'];
      $view->vars['types'] = $options['file_types'];
      $view->vars['label'] = $options['btn_label'];
      $view->vars['param_name'] = $this->paramName;
      $view->vars['debug'] = true === $options['debug'] ? 'true': 'false';
      $view->vars['widget_name'] = $form->getName();
      $view->vars['session_id'] = session_id();
      $view->vars['form_type'] = $options['upload_form_type'];
    }
}
