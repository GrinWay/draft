<?php

namespace App\EventListener\Command;

use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
class BeforeCommandEventListener
{
    public function __invoke(ConsoleCommandEvent $event)
    {
//        $event->disableCommand();
        return;

        $output = $event->getOutput();
        $command = $event->getCommand();
        $formatter = $command->getHelper('formatter');

        $output->writeln([
            $formatter->formatSection('COMMAND', \sprintf('%s', $command->getName())),
        ]);
    }
}
