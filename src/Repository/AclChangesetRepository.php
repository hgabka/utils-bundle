<?php

namespace Hgabka\UtilsBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Hgabka\UtilsBundle\Entity\AclChangeset;

/**
 * ACL changeset repository.
 */
class AclChangesetRepository extends EntityRepository
{
    /**
     * Find a changeset with status RUNNING.
     *
     * @return AclChangeset|null
     */
    public function findRunningChangeset()
    {
        $qb = $this->createQueryBuilder('ac')
            ->select('ac')
            ->where('ac.status = :status')
            ->addOrderBy('ac.id', 'ASC')
            ->setMaxResults(1)
            ->setParameter('status', AclChangeset::STATUS_RUNNING);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Fetch the oldest acl changeset for state NEW.
     *
     * @return AclChangeset|null
     */
    public function findNewChangeset()
    {
        $qb = $this->createQueryBuilder('ac')
            ->select('ac')
            ->where('ac.status = :status')
            ->addOrderBy('ac.id', 'ASC')
            ->setMaxResults(1)
            ->setParameter('status', AclChangeset::STATUS_NEW);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Check if there are pending changesets.
     *
     * @return bool
     */
    public function hasPendingChangesets()
    {
        $qb = $this->createQueryBuilder('ac')
            ->select('count(ac)')
            ->where('ac.status = :status')
            ->setParameter('status', AclChangeset::STATUS_NEW);

        return 0 !== $qb->getQuery()->getSingleScalarResult();
    }
}
