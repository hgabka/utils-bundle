<?php

namespace Hgabka\UtilsBundle\Command;

use Hgabka\UtilsBundle\Util\UserManipulator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * @author Lenar Lõhmus <lenar@city.ee>
 *
 * @internal
 */
abstract class RoleCommand extends Command
{
    public function __construct(private readonly UserManipulator $userManipulator)
    {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDefinition([
                new InputArgument('username', InputArgument::REQUIRED, 'The username'),
                new InputArgument('role', InputArgument::OPTIONAL, 'The role'),
                new InputOption('super', null, InputOption::VALUE_NONE, 'Instead specifying role, use this to quickly add the super administrator role'),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username = $input->getArgument('username');
        $role = $input->getArgument('role');
        $super = (true === $input->getOption('super'));

        if (null !== $role && $super) {
            throw new \InvalidArgumentException('You can pass either the role or the --super option (but not both simultaneously).');
        }

        if (null === $role && !$super) {
            throw new \RuntimeException('Not enough arguments.');
        }

        $manipulator = $this->userManipulator;
        $this->executeRoleCommand($manipulator, $output, $username, $super, $role);

        return Command::SUCCESS;
    }

    /**
     * @see Command
     *
     * @param string $username
     * @param bool   $super
     * @param string $role
     */
    abstract protected function executeRoleCommand(UserManipulator $manipulator, OutputInterface $output, $username, $super, $role): void;

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $questions = [];

        if (!$input->getArgument('username')) {
            $question = new Question('Please choose a username:');
            $question->setValidator(function ($username) {
                if (empty($username)) {
                    throw new \Exception('Username can not be empty');
                }

                return $username;
            });
            $questions['username'] = $question;
        }

        if ((true !== $input->getOption('super')) && !$input->getArgument('role')) {
            $question = new Question('Please choose a role:');
            $question->setValidator(function ($role) {
                if (empty($role)) {
                    throw new \Exception('Role can not be empty');
                }

                return $role;
            });
            $questions['role'] = $question;
        }

        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }
}
