<?php

namespace Hgabka\UtilsBundle\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;

class SortableCRUDController extends CRUDController
{
    public function sortingAction(Request $request, ManagerRegistry $doctrine): Response
    {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createNotFoundException();
        }

        if (!$this->admin->hasAccess('list')) {
            return new Response();
        }

        if (!$request->request->has('positions')) {
            return new Response();
        }
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        foreach ($request->request->get('positions') as $id => $position) {
            $object = $this->admin->getObject($id);
            if ($object) {
                $propertyAccessor->setValue($object, $this->admin->getSortField(), $position);
            }
        }

        $this->doctrine->getManager()->flush();

        return new Response();
    }
}
