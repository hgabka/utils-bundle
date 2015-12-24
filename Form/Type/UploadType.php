<?php
  
namespace HG\UtilsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;


class UploadType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'widget_name' => 'uploadify_upload',
            'csrf_protection' => false
            
            ));
    }
    
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add($options['widget_name'], 'file', array('constraints' => array(
          new NotBlank()
        )));
    }
    
    public function getName()
    {
        return 'uploadify_upload';
    }
  
}
