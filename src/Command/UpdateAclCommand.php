<?php

namespace Hgabka\UtilsBundle\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Hgabka\NodeBundle\Command\InitAclCommand;
use Hgabka\NodeBundle\Entity\Node;
use Hgabka\UtilsBundle\Helper\Security\Acl\Permission\PermissionMap;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Security\Acl\Domain\Acl;
use Symfony\Component\Security\Acl\Domain\Entry;
use Symfony\Component\Security\Acl\Domain\ObjectIdentityRetrievalStrategy;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;

#[AsCommand(name: 'hgabka:acl:update', description: 'Permissions update of ACL entries for all nodes for given role', hidden: false)]
class UpdateAclCommand extends Command
{
    /** @var ObjectIdentityRetrievalStrategy */
    protected $oiaStrategy;

    /** @var AclProviderInterface */
    private $aclProvider;

    /**
     * @param ObjectIdentityRetrievalStrategy $oiaStrategy
     * @param EntityManagerInterface          $entityManager
     * @param AclProviderInterface            $aclProvider
     * @param array                           $roles
     */
    public function __construct(protected readonly EntityManagerInterface $entityManager, protected readonly PermissionMap $permissionMap, protected readonly array $roles)
    {
        parent::__construct();
    }

    public function setAclProvider(AclProviderInterface $provider)
    {
        $this->aclProvider = $provider;
    }

    /**
     * @param ObjectIdentityRetrievalStrategy $oiaStrategy
     *
     * @return InitAclCommand
     */
    public function setOiaStrategy($oiaStrategy)
    {
        $this->oiaStrategy = $oiaStrategy;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setHelp('The <info>hgabka:update:acl</info> will update ACL entries for the nodes of the current project' .
                'with given role and permissions');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        // Select Role
        $roles = $this->roles;
        $question = new ChoiceQuestion('Select role', array_keys($roles));
        $question->setErrorMessage('Role %s is invalid.');
        $role = $helper->ask($input, $output, $question);

        // Select Permission(s)
        $permissionMap = $this->permissionMap;
        $question = new ChoiceQuestion(
            'Select permissions(s) (separate by ",")',
            $permissionMap->getPossiblePermissions()
        );
        $question->setMultiselect(true);
        $mask = array_reduce($helper->ask($input, $output, $question), function ($a, $b) use ($permissionMap) {
            return $a | $permissionMap->getMasks($b, null)[0];
        }, 0);

        // @var EntityManager $em
        $em = $this->entityManager;
        // @var MutableAclProviderInterface $aclProvider
        $aclProvider = $this->aclProvider;
        // @var ObjectIdentityRetrievalStrategyInterface $oidStrategy
        $oidStrategy = $this->oiaStrategy;

        // Fetch all nodes & grant access
        $nodes = $em->getRepository(Node::class)->findAll();

        foreach ($nodes as $node) {
            $objectIdentity = $oidStrategy->getObjectIdentity($node);

            /** @var Acl $acl */
            $acl = $aclProvider->findAcl($objectIdentity);
            $securityIdentity = new RoleSecurityIdentity($role);

            /** @var Entry $ace */
            foreach ($acl->getObjectAces() as $index => $ace) {
                if (!$ace->getSecurityIdentity()->equals($securityIdentity)) {
                    continue;
                }
                $acl->updateObjectAce($index, $mask);

                break;
            }
            $aclProvider->updateAcl($acl);
        }
        $output->writeln(\count($nodes) . ' nodes processed.');

        return Command::SUCCESS;
    }
}
