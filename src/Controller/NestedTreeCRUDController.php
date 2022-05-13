<?php

namespace Hgabka\UtilsBundle\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Hgabka\MediaBundle\Entity\Folder;
use Hgabka\UtilsBundle\Entity\NestedTreeEntityInterface;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NestedTreeCRUDController extends CRUDController
{
    /** @var ManagerRegistry */
    protected $doctrine;

    /**
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function listAction(Request $request): Response
    {
        $this->admin->checkAccess('list');

        $preResponse = $this->preList($request);
        if (null !== $preResponse) {
            return $preResponse;
        }

        $class = $this->admin->getClass();
        $em = $this->doctrine;
        $repo = $em->getRepository($class);

        $folderId = $request->get($this->admin->getIdParameter());

        // @var Folder $folder
        $folder = empty($folderId) ? $repo->findOneBy(['parent' => null]) : $repo->find($folderId);

        $sub = $this->admin->getNewInstance();
        $sub->setParent($folder);
        $this->admin->setSubject($sub);
        $subFormBuilder = $this->admin->getFormBuilder();
        $subForm = $this->admin->getSubFormBuilder()->getForm();
        $subForm->setData($sub);

        $this->admin->setSubject($folder);
        $editForm = $this->admin->getForm();
        $editForm->setData($folder);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted()) {
            $this->admin->checkAccess('edit', $folder);

            if ($editForm->isValid()) {
                $this->admin->update($folder);
                $repo->save($folder);
                $em->getManager()->flush();
                $this->addFlash(
                    'sonata_flash_success',
                    $this->trans('kuma_admin_list.messages.edit_success')
                );

                return new RedirectResponse(
                    $this->admin->generateUrl('list', [$this->admin->getIdParameter() => $folderId])
                );
            }
            $this->addFlash(
                'sonata_flash_error',
                $this->trans('kuma_admin_list.messages.edit_error')
            );

            return new RedirectResponse(
                $this->admin->generateUrl('list', [$this->admin->getIdParameter() => $folderId])
            );
        }

        return $this->render(
            '@HgabkaUtils/Admin/NestedTree/list.html.twig',
            [
                'repo' => $repo,
                'subform' => $subForm->createView(),
                'editform' => $editForm->createView(),
                'object' => $folder,
                'admin' => $this->admin,
                'base_template' => $this->getBaseTemplate(),
            ]
        );
    }

    public function reorderAction(Request $request): Response
    {
        $this->admin->checkAccess('reorder');
        $folders = [];
        $nodeIds = $request->get('nodes');
        $changeParents = $request->get('parent');

        $em = $this->doctrine->getManager();
        $class = $this->admin->getClass();
        $repository = $em->getRepository($class);

        foreach ($nodeIds as $id) {
            // @var Folder $folder
            $folder = $repository->find($id);
            $folders[] = $folder;
        }

        if (!empty($changeParents)) {
            foreach ($folders as $id => $folder) {
                $newParentId = isset($changeParents[$folder->getId()]) ? $changeParents[$folder->getId()] : null;
                if ($newParentId) {
                    $parent = $repository->find($newParentId);
                    if ($parent) {
                        $folder->setParent($parent);
                    }
                }
            }
            $em->flush();
        }

        foreach ($folders as $id => $folder) {
            $repository->moveDown($folder, true);
        }

        $em->flush();

        return new JsonResponse(
            [
                'Success' => 'done',
            ]
        );
    }

    public function subCreateAction(Request $request): Response
    {
        $this->admin->checkAccess('create');
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $class = $this->admin->getClass();
        $repo = $em->getRepository($class);

        $folder = $this->admin->getNewInstance();
        $this->admin->setSubject($folder);
        $form = $this->admin->getSubFormBuilder()->getForm();
        $form->setData($folder);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $submittedObject = $form->getData();

                $this->admin->checkAccess('create', $submittedObject);
                $newObject = $this->admin->create($submittedObject);
                $repo->save($newObject);
                $em->flush();
                $this->addFlash(
                    'sonata_flash_success',
                    $this->trans('kuma_admin_list.messages.add_success')
                );
                $redirect = $this->admin->generateUrl('list')
                ;

                return new RedirectResponse(
                    $this->admin->generateUrl(
                        'list',
                        [
                            $this->admin->getIdParameter() => $newObject->getId(),
                        ]
                    )
                );
            }
            $this->addFlash(
                'sonata_flash_error',
                $this->trans('kuma_admin_list.messages.add_error')
            );
            $redirect = $this->admin->generateUrl('list');

            return new RedirectResponse(
                $this->admin->generateUrl(
                    'list',
                    [
                            $this->admin->getIdParameter() => $newObject->getId(),
                        ]
                )
            );
        }

        return $this->render(
            '@HgabkaUtils/Admin/NestedTree/_addsub-modal.html.twig',
            [
                'admin' => $this->admin,
                'subform' => $form->createView(),
                'object' => $folder,
                'parent' => $parent,
            ]
        );
    }

    public function deleteAction(Request $request): Response
    {
        $this->assertObjectExists($request, true);

        $id = $request->get($this->admin->getIdParameter());
        \assert(null !== $id);
        $object = $this->admin->getObject($id);
        \assert(null !== $object);
        /** @var NestedTreeEntityInterface $object */
        $object = $this->admin->getObject($id);
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        $this->admin->checkAccess('delete', $object);

        $preResponse = $this->preDelete($request, $object);
        if (null !== $preResponse) {
            return $preResponse;
        }

        if (!$object->isDeleteable()) {
            $this->addFlash(
                'sonata_flash_error',
                $this->trans('kuma_admin_list.messages.delete_error')
            );

            return new RedirectResponse(
                $this->admin->generateUrl(
                    'list',
                    [
                        $this->admin->getIdParameter() => $id,
                    ]
                )
            );
        }

        $parentObject = $object->getParent();

        try {
            $this->admin->delete($object);

            if ($this->isXmlHttpRequest()) {
                return $this->renderJson(['result' => 'ok'], Response::HTTP_OK, []);
            }

            $this->addFlash(
                'sonata_flash_success',
                $this->trans('kuma_admin_list.messages.delete_success')
            );
            $id = $parentObject ? $parentObject->getId() : null;
        } catch (ModelManagerException $e) {
            $this->handleModelManagerException($e);

            if ($this->isXmlHttpRequest()) {
                return $this->renderJson(['result' => 'error'], Response::HTTP_OK, []);
            }

            $this->addFlash(
                'sonata_flash_error',
                $this->trans('kuma_admin_list.messages.delete_error')
            );
        }

        return new RedirectResponse(
            $this->admin->generateUrl(
                'list',
                empty($id)
                    ? []
                    : [
                    $this->admin->getIdParameter() => $id,
                ]
            )
        );
    }
}
