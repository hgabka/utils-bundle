<?php

namespace Hgabka\UtilsBundle\Command;

use Hgabka\UtilsBundle\Util\UserManipulator;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Antoine Hérault <antoine.herault@gmail.com>
 * @author Lenar Lõhmus <lenar@city.ee>
 *
 * @internal
 * @final
 */
class DemoteUserCommand extends RoleCommand
{
    protected static $defaultName = 'hgabka:backend-user:demote';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName(self::$defaultName)
            ->setDescription('Demote a user by removing a role')
            ->setHelp(
                <<<'EOT'
                    The <info>fos:user:demote</info> command demotes a user by removing a role

                      <info>php %command.full_name% matthieu ROLE_CUSTOM</info>
                      <info>php %command.full_name% --super matthieu</info>
                    EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function executeRoleCommand(UserManipulator $manipulator, OutputInterface $output, $username, $super, $role)
    {
        if ($super) {
            $manipulator->demote($username);
            $output->writeln(sprintf('User "%s" has been demoted as a simple user. This change will not apply until the user logs out and back in again.', $username));
        } else {
            if ($manipulator->removeRole($username, $role)) {
                $output->writeln(sprintf('Role "%s" has been removed from user "%s". This change will not apply until the user logs out and back in again.', $role, $username));
            } else {
                $output->writeln(sprintf('User "%s" didn\'t have "%s" role.', $username, $role));
            }
        }
    }
}
