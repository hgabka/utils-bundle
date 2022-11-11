<?php

namespace Hgabka\UtilsBundle\Command;

use Hgabka\UtilsBundle\Util\UserManipulator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'hgabka:backend-user:promote', description: 'Promotes a user by adding a role', hidden: false)]
class PromoteUserCommand extends RoleCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setHelp(
                <<<'EOT'
                    The <info>hgabka:backend-user:promote</info> command promotes a user by adding a role
                    EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function executeRoleCommand(UserManipulator $manipulator, OutputInterface $output, $username, $super, $role)
    {
        if ($super) {
            $manipulator->promote($username);
            $output->writeln(sprintf('User "%s" has been promoted as a super administrator. This change will not apply until the user logs out and back in again.', $username));
        } else {
            if ($manipulator->addRole($username, $role)) {
                $output->writeln(sprintf('Role "%s" has been added to user "%s". This change will not apply until the user logs out and back in again.', $role, $username));
            } else {
                $output->writeln(sprintf('User "%s" did already have "%s" role.', $username, $role));
            }
        }
    }
}
