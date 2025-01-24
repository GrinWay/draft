<?php

namespace App\EventListener\Kernel;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;

#[AsEventListener]
class RequestEventListener
{
    public function __invoke(RequestEvent $event)
    {
        $request = $event->getRequest();

//        \dump($request->getUri());
    }
}
