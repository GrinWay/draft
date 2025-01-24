<?php

namespace App\Messenger\Command\ProcessSomething\Handler;

use App\Entity\Todo;
use App\Messenger\Command\ProcessSomething\ProcessSomethingMessage;
use App\Messenger\Event\ProcessSomething\SomethingProcessed;
use App\Repository\TodoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

class ProcessSomethingMessageHandler
{
    public function __construct(
        private readonly MessageBusInterface      $appEventBus,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly EntityManagerInterface   $em,
        private readonly TodoRepository           $todoRepo,
    )
    {
    }

    public function __invoke(ProcessSomethingMessage $message)
    {
        $this->do($message);

        $this->appEventBus->dispatch(new SomethingProcessed());
    }

    private function do(ProcessSomethingMessage $message)
    {
//        $this->eventDispatcher->dispatch(new GenericEvent($message));

//        $oneTodo = $this->todoRepo->findOneBy([]);
//        assert($oneTodo instanceof Todo);
//
//        \dump($oneTodo);
//        $oneTodo->setTitle('CHANGED');
//
        \dump(__METHOD__);
    }
}
