<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'tio',
    description: '',
)]
class TwoInOneCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
//        $this
//            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
//        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $inputForAnotherCommand1 = new ArrayInput([
            'command' => 'cache:clear',
        ]);
        $inputForAnotherCommand2 = new ArrayInput([
            'command' => 'assets:install',
        ]);
        $output1 = new BufferedOutput();
        $output2 = new BufferedOutput();

        $inputForAnotherCommand1->setInteractive(false);
        $inputForAnotherCommand2->setInteractive(false);

        $this->getApplication()->doRun($inputForAnotherCommand1, $output1);
        $this->getApplication()->doRun($inputForAnotherCommand2, $output2);

        $firstCommandOutput = \sprintf('First command output is: %s', $output1->fetch());
        $secondCommandOutput = \sprintf('Second command output is: %s', $output2->fetch());
        $io->success('Two in one command was executed');
        $io->text([
            $firstCommandOutput,
            $secondCommandOutput,
        ]);
        return Command::SUCCESS;
    }
}
