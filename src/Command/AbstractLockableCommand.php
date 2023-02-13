<?php

declare(strict_types=1);

namespace Hgabka\UtilsBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractLockableCommand extends Command
{
    use LockableTrait;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (!$this->lock()) {
            $io->error('The command is already running');

            return Command::SUCCESS;
        }

        $result = $this->executeLocked($input, $output);

        $this->release();

        return $result;
    }

    abstract protected function executeLocked(InputInterface $input, OutputInterface $output): int;
}
