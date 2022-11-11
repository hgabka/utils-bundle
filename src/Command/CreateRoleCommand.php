<?php

namespace Hgabka\UtilsBundle\Command;

use Doctrine\ORM\EntityManager;
use Hgabka\UtilsBundle\Entity\Role;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'hgabka:role:create', description: 'Creates a role', hidden: false)]
class CreateRoleCommand extends ContainerAwareCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setDefinition([
                new InputArgument('role', InputArgument::REQUIRED, 'The role'),
            ])
            ->setHelp(
                <<<'EOT'
                    The <info>hgabka:role:create</info> command creates a role:

                      <info>php bin/console kuma:role:create ROLE_ADMIN</info>

                    <comment>Note:</comment> The ROLE_ prefix will be added if you don't provide it

                      <info>php bin/console kuma:role:create ADMIN</info>

                    will create ROLE_ADMIN.

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

        $roleName = strtoupper($input->getArgument('role'));
        if ('ROLE_' !== substr($roleName, 0, 5)) {
            $roleName = 'ROLE_' . $roleName;
        }

        $role = new Role($roleName);
        $em->persist($role);
        $em->flush();

        $output->writeln(sprintf('Created role <comment>%s</comment>', $roleName));
    }
}
