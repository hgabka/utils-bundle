<?php

namespace Hgabka\UtilsBundle\Repository;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository as BaseTreeRepository;
use Hgabka\UtilsBundle\Entity\NestedTreeEntityInterface;

class NestedTreeRepository extends BaseTreeRepository
{
    public function getRoot()
    {
        $rootNodes = $this->getRootNodes();
        if (\count($rootNodes)) {
            return $rootNodes[0];
        }

        return null;
    }

    /**
     * @param Category $category Kategória
     *
     * @throws \Exception
     */
    public function save(NestedTreeEntityInterface $object)
    {
        $em = $this->getEntityManager();
        $parent = $object->getParent();

        $em->beginTransaction();

        try {
            if (null !== $parent && null === $object->getId()) {
                $this->persistInOrderedTree($object, $parent);
            } else {
                $em->persist($object);
            }
            $em->flush();
            $em->commit();
        } catch (\Exception $e) {
            $em->rollback();

            throw $e;
        }
    }

    public function delete(Category $category)
    {
        $em = $this->getEntityManager();

        $this->deleteChildren($category);
        $em->remove($category);
        $em->flush();
    }

    /**
     * Used as querybuilder for Category entity selectors.
     *
     * @param Category $ignoreSubtree Category (with children) that has to be filtered out (optional)
     *
     * @return QueryBuilder
     */
    public function selectTreeQueryBuilder(NestedTreeEntityInterface $ignoreSubtree = null)
    {
        /** @var QueryBuilder $qb */
        $qb = $this->createQueryBuilder('f');
        $qb->orderBy('f.lft');

        // Fetch all Categorys except the current one and its children
        if (null !== $ignoreSubtree && null !== $ignoreSubtree->getId()) {
            $orX = $qb->expr()->orX();
            $orX->add('f.rgt > :right')
                ->add('f.lft < :left')
            ;

            $qb->andWhere($orX)
               ->setParameter('left', $ignoreSubtree->getLeft())
               ->setParameter('right', $ignoreSubtree->getRight())
            ;
        }

        return $qb;
    }

    public function getParentIds(NestedTreeEntityInterface $object)
    {
        /** @var QueryBuilder $qb */
        $qb = $this->getPathQueryBuilder($object)
                   ->select('node.id')
        ;

        $result = $qb->getQuery()->getScalarResult();
        $ids = array_map('current', $result);

        return $ids;
    }

    public function getRootFor(NestedTreeEntityInterface $object)
    {
        $ids = $this->getParentIds($object);

        return isset($ids[0]) ? $this->getObject($ids[0]) : null;
    }

    /**
     * @param Category $rootCategory
     * @param bool     $includeNode
     *
     * @return array|string
     */
    public function getHierarchy(NestedTreeEntityInterface $root = null, $includeNode = false)
    {
        return $this->childrenHierarchy($root, false, [], $includeNode);
    }

    /**
     * @param int $limit
     *
     * @return array
     */
    public function getAllObjectsQueryBuilder($limit = null)
    {
        $qb = $this->createQueryBuilder('object')
                   ->select('object')
                   ->where('object.parent is null')
        ;

        if (false === (null === $limit)) {
            $qb->setMaxResults($limit);
        }

        return $qb;
    }

    /**
     * @param int $limit
     *
     * @return array
     */
    public function getAllObjects($limit = null)
    {
        return $this->getAllObjectsQueryBuilder()->getQuery()->getResult();
    }

    /**
     * @param int   $categoryId
     * @param mixed $id
     *
     * @throws EntityNotFoundException
     *
     * @return object
     */
    public function getObject($id)
    {
        $category = $this->find($id);
        if (!$category) {
            throw new EntityNotFoundException();
        }

        return $category;
    }

    public function getChoices()
    {
        $ret = [];

        $objects = $this
            ->createQueryBuilder('c')
            ->where('c.lvl = :lvl')
            ->setParameter('lvl', 2)
            ->orderBy('c.lft')
            ->getQuery()
            ->getResult()
        ;

        foreach ($objects as $cat) {
            /** @var NestedTreeEntityInterface $cat */
            if (!$cat->getParent()) {
                continue;
            }

            if (!\array_key_exists((string) $cat->getParent(), $ret)) {
                $ret[(string) $cat->getParent()] = [];
            }

            $ret[(string) $cat->getParent()][$cat->getId()] = $cat;
        }

        return $ret;
    }

    /**
     * @param $parent
     */
    private function persistInOrderedTree(NestedTreeEntityInterface $object, $parent)
    {
        // Find where to insert the new item
        $children = $parent->getChildren(true);
        if ($children->isEmpty()) {
            // No children yet - insert as first child
            $this->persistAsFirstChildOf($object, $parent);
        } else {
            $this->persistAsLastChildOf($object, $parent);
        }
    }

    private function deleteChildren(NestedTreeEntityInterface $object)
    {
        $em = $this->getEntityManager();

        /** @var Category $child */
        foreach ($object->getChildren() as $child) {
            $this->deleteChildren($child);
            $em->remove($child);
        }
        $em->flush();
    }
}
