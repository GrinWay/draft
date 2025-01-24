<?php

namespace App\Controller;

use App\Messenger\Command\ProcessSomething\ProcessSomethingMessage;
use App\Messenger\Event\SomethingProcessed;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class MessengerController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $bus,
        private readonly MessageBusInterface $appEventBus,
    )
    {
    }

    #[Route('/messenger', name: 'app_messenger')]
    public function index(): Response
    {
        $processSomethingMessage = new ProcessSomethingMessage();
//        $processSomethingMessage->name = 'not blank';

        $this->bus->dispatch($processSomethingMessage);

        $params = [
        ];
        return $this->render('messenger/index.html.twig', $params);
    }
}
