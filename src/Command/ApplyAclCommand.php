<?php

namespace Hgabka\UtilsBundle\Command;

use Doctrine\ORM\EntityManager;
use Hgabka\UtilsBundle\Entity\AclChangeset;
use Hgabka\UtilsBundle\Helper\Security\Acl\Permission\PermissionAdmin;
use Hgabka\UtilsBundle\Helper\Shell\Shell;
use Hgabka\UtilsBundle\Repository\AclChangesetRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Symfony CLI command to apply the {@link AclChangeSet} with status {@link AclChangeSet::STATUS_NEW} to their entities.
 */
class ApplyAclCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Shell
     */
    private $shellHelper;

    /**
     * Configures the command.
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('hgabka:acl:apply')
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
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->shellHelper = $this->getContainer()->get('hgabka_utils.shell');
        // @var PermissionAdmin $permissionAdmin
        $permissionAdmin = $this->getContainer()->get('hgabka_utils.permissionadmin');

        // Check if another ACL apply process is currently running & do nothing if it is
        if ($this->isRunning()) {
            return;
        }
        // @var AclChangesetRepository $aclRepo
        $aclRepo = $this->em->getRepository('KunstmaanAdminBundle:AclChangeset');
        do {
            // @var AclChangeset $changeset
            $changeset = $aclRepo->findNewChangeset();
            if (null === $changeset) {
                break;
            }
            $changeset->setPid(getmypid());
            $changeset->setStatus(AclChangeset::STATUS_RUNNING);
            $this->em->persist($changeset);
            $this->em->flush($changeset);

            $entity = $this->em->getRepository($changeset->getRefEntityName())->find($changeset->getRefId());
            $permissionAdmin->applyAclChangeset($entity, $changeset->getChangeset());

            $changeset->setStatus(AclChangeset::STATUS_FINISHED);
            $this->em->persist($changeset);
            $this->em->flush($changeset);

            $hasPending = $aclRepo->hasPendingChangesets();
        } while ($hasPending);
    }

    /**
     * @return bool
     */
    private function isRunning()
    {
        // Check if we have records in running state, if so read PID & check if process is active
        // @var AclChangeset $runningAclChangeset
        $runningAclChangeset = $this->em->getRepository('KunstmaanAdminBundle:AclChangeset')->findRunningChangeset();
        if (null !== $runningAclChangeset) {
            // Found running process, check if PID is still running
            if (!$this->shellHelper->isRunning($runningAclChangeset->getPid())) {
                // PID not running, process probably failed...
                $runningAclChangeset->setStatus(AclChangeset::STATUS_FAILED);
                $this->em->persist($runningAclChangeset);
                $this->em->flush($runningAclChangeset);
            }
        }

        return false;
    }
}
