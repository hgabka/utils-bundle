<?php

namespace Hgabka\UtilsBundle\Command;

use Symfony\Bundle\MakerBundle\Exception\RuntimeCommandException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'hgabka:user:demote', description: 'Demotes user', hidden: false)]
class DemoteGeneralUserCommand extends AbstractUserRoleCommand
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = $this->getUserFromArguments($input);
        $role = $input->getArgument('role');

        $roles = $user->getRoles();

        if (empty($roles) || !in_array($role, $roles, true)) {
            throw new RuntimeCommandException('User does not have this role');
        }

        $key = array_search($role, $roles, true);
        unset($roles[$key]);

        $user->setRoles($roles);

        $this->manager->flush();

        return Command::SUCCESS;
    }
}
