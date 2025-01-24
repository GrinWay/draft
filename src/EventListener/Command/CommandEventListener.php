<?php

namespace App\EventListener\Command;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(ConsoleEvents::TERMINATE)]
class CommandEventListener
{
    public function __invoke(ConsoleTerminateEvent $event): void {
//        return;
        $command = $event->getCommand();
        $name = $command->getName() ?: $command->getDefaultName();
        $message = \sprintf('__COMMAND "%s" TERMINATED: with code %s__', $name, $event->getExitCode());
        \dump($message);
    }
}
