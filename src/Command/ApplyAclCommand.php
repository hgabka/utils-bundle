<?php

namespace Hgabka\UtilsBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Hgabka\UtilsBundle\Entity\AclChangeset;
use Hgabka\UtilsBundle\Helper\Security\Acl\Permission\PermissionAdmin;
use Hgabka\UtilsBundle\Helper\Shell\Shell;
use Hgabka\UtilsBundle\Repository\AclChangesetRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Symfony CLI command to apply the {@link AclChangeSet} with status {@link AclChangeSet::STATUS_NEW} to their entities.
 */
class ApplyAclCommand extends Command
{
    protected static $defaultName = 'hgabka:acl:apply';

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * @var Shell
     */
    private $shellHelper;

    /** @var PermissionAdmin */
    private $permissionAdmin;

    /**
     * @param EntityManagerInterface $entityManager
     * @param Shell                  $shellHelper
     */
    public function __construct(EntityManagerInterface $entityManager, Shell $shellHelper, PermissionAdmin $permissionAdmin)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->shellHelper = $shellHelper;
        $this->permissionAdmin = $permissionAdmin;
    }

    /**
     * Configures the command.
     */
    protected function configure()
    {
        parent::configure();

        $this->setName(static::$defaultName)
             ->setDescription('Apply ACL changeset.')
             ->setHelp('The <info>hgabka:acl:apply</info> can be used to apply an ACL changeset recursively, changesets are fetched from the database.');
    }

    /**
     * Apply the {@link AclChangeSet} with status {@link AclChangeSet::STATUS_NEW} to their entities.
     *
     * @param InputInterface  $input  The input
     * @param OutputInterface $output The output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Check if another ACL apply process is currently running & do nothing if it is
        if ($this->isRunning()) {
            return Command::SUCCESS;
        }
        // @var AclChangesetRepository $aclRepo
        $aclRepo = $this->entityManager->getRepository(AclChangeset::class);
        do {
            // @var AclChangeset $changeset
            $changeset = $aclRepo->findNewChangeset();
            if (null === $changeset) {
                break;
            }
            $changeset->setPid(getmypid());
            $changeset->setStatus(AclChangeset::STATUS_RUNNING);
            $this->entityManager->persist($changeset);
            $this->entityManager->flush($changeset);

            $entity = $this->entityManager->getRepository($changeset->getRefEntityName())->find($changeset->getRefId());
            $this->permissionAdmin->applyAclChangeset($entity, $changeset->getChangeset());

            $changeset->setStatus(AclChangeset::STATUS_FINISHED);
            $this->entityManager->persist($changeset);
            $this->entityManager->flush($changeset);

            $hasPending = $aclRepo->hasPendingChangesets();
        } while ($hasPending);

        return Command::SUCCESS;
    }

    /**
     * @return bool
     */
    private function isRunning()
    {
        // Check if we have records in running state, if so read PID & check if process is active
        // @var AclChangeset $runningAclChangeset
        $runningAclChangeset = $this->entityManager->getRepository(AclChangeset::class)->findRunningChangeset();
        if (null !== $runningAclChangeset) {
            // Found running process, check if PID is still running
            if (!$this->shellHelper->isRunning($runningAclChangeset->getPid())) {
                // PID not running, process probably failed...
                $runningAclChangeset->setStatus(AclChangeset::STATUS_FAILED);
                $this->entityManager->persist($runningAclChangeset);
                $this->entityManager->flush($runningAclChangeset);
            }
        }

        return false;
    }
}
