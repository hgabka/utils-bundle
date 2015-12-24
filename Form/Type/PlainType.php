<?php

namespace HG\UtilsBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PlainType extends AbstractType
{
  public function getParent()
  {
      return 'text';
  }

  public function getName()
  {
      return 'plain';
  }
}
