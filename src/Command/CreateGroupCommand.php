<?php

namespace Hgabka\UtilsBundle\Command;

use Doctrine\ORM\EntityManager;
use Hgabka\UtilsBundle\Entity\Group;
use Hgabka\UtilsBundle\Entity\Role;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'hgabka:group:create', description: 'Creates a user group', hidden: false)]
class CreateGroupCommand extends ContainerAwareCommand
{
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDefinition([
                new InputArgument('group', InputArgument::REQUIRED, 'The group'),
                new InputOption('role', null, InputOption::VALUE_OPTIONAL, 'Role(s) (comma separated list if you want to specifiy multiple roles)'),
            ])
            ->setHelp(
                <<<'EOT'
                    The <info>hgabka:group:create</info> command creates a group:

                      <info>php bin/console hgabka:group:create Administrators</info>

                    You can specify a list of roles to attach to this group by specifying the
                    optional --roles parameter, providing a comma separated list of roles :

                      <info>php bin/console hgabka:group:create --role=admin,guest Administrators</info>

                    <comment>Note:</comment> The ROLE_ prefix will be added if you don't provide it AND you must make
                    sure the roles already exist!

                    EOT
            );
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input  The input
     * @param OutputInterface $output The output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // @var EntityManager $em
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $groupName = $input->getArgument('group');
        $roleNames = $input->getOption('role');
        $group = new Group($groupName);

        if (!empty($roleNames)) {
            // Roles were provided, so attach them to the group
            $roleNames = explode(',', strtoupper($roleNames));
            foreach ($roleNames as $roleName) {
                if ('ROLE_' !== substr($roleName, 0, 5)) {
                    $roleName = 'ROLE_' . $roleName;
                }
                // @var Role $role
                $role = $em->getRepository('KunstmaanAdminBundle:Role')->findOneBy(['role' => $roleName]);
                $group->addRole($role);
            }
        }
        $em->persist($group);
        $em->flush();

        $output->writeln(sprintf('Created group <comment>%s</comment>', $groupName));
    }
}
