<?php

namespace HG\UtilsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use HG\UtilsBundle\Utils\HGUtils;
use Symfony\Component\Validator\Constraints\NotBlank;

class DefaultController extends Controller
{
  public function uploadifyAction(Request $request, $name, $formType, $controller = '')
  {
    $form = $this->createForm($formType, null, array('widget_name' => $name));
    
    $form->handleRequest($request);
    
    if (!$form->isValid())
    {
      return new Response(json_encode(array('valid' => false, 'msgs' => $form[$name]->getErrorsAsString())));
    }
     
    $file = $form[$name]->getData();
    
    if (!empty($controller) && $controller !== 'null')
    {
      return $this->forward($controller, array('name' => $name, 'file' => $file));
    }

    return new Response(json_encode(array('valid' => false, 'msgs' => 'Ervenytelen response')));
  }


  public function uploadifyRenderAction($file, $name)
  {
    $fs = new Filesystem();
    $uploadDir = $this->container->getParameter('hg_utils.upload_dir');
    $uDir = $uploadDir.'/'.$name;
    if (!$fs->exists($uDir))
    {
      try
      {
        $fs->mkdir($uDir);
      }
      catch (IOException $e)
      {
        return new Response(json_encode(array('valid' => false, 'msgs' => $e->getMessage())));
      }
    }

    $origName = pathinfo($file->getClientOriginalName(),  PATHINFO_FILENAME);
    $newFilename = HGUtils::slugify($origName).'_'.date('YmdHis').'.'.$file->getClientOriginalExtension();

    $file->move($uDir, $newFilename);

    return new Response(json_encode(array('valid' => true, 'html' => $this->renderView('HGUtilsBundle:Default:uploadifyRender.html.twig', array(
	  'dirname' => $this->container->getParameter('hg_utils.upload_dir_name').'/'.$name, 
	  'filename' => $newFilename
	  )))));
  }
}
