<?php

namespace App\Command;

use App\Service\MySqlUtils;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:backup',
    description: '',
)]
class BackupCommand extends Command
{
    public function __construct(
        private readonly MySqlUtils $mysqlUtils,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->mysqlUtils->backup();

        return Command::SUCCESS;
    }
}
