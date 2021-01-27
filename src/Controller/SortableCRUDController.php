<?php

namespace Hgabka\UtilsBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;

class SortableCRUDController extends CRUDController
{
    public function sortingAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw $this->createNotFoundException();
        }

        if (!$this->admin->hasAccess('list')) {
            return new Response();
        }

        if (!$this->getRequest()->request->has('positions')) {
            return new Response();
        }
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        foreach ($this->getRequest()->request->get('positions') as $id => $position) {
            $object = $this->admin->getObject($id);
            if ($object) {
                $propertyAccessor->setValue($object, $this->admin->getSortField(), $position);
            }
        }

        $this->getDoctrine()->getManager()->flush();

        return new Response();
    }
}
