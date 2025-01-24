<?php

namespace App\Command;

use App\Repository\TodoRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'test',
    description: '',
    hidden: true,
)]
class TestCommand extends Command
{
    use LockableTrait;

    public function __construct(
        private readonly UrlHelper $urlHelper,
        private readonly TodoRepository $todoRepo,
    )
    {
        parent::__construct();
    }

    protected function configure()
    {
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $list = $this->todoRepo->findBy([]);

        $io->success(\array_map(static function ($el) {
            return $el->getTitle();
        }, $list));

        return Command::SUCCESS;
    }
}
