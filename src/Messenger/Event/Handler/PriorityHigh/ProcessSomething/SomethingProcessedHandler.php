<?php

namespace App\Messenger\Event\Handler\PriorityHigh\ProcessSomething;

use App\Messenger\Event\ProcessSomething\SomethingProcessed;
use Psr\Log\LoggerInterface;

class SomethingProcessedHandler
{
    public function __construct(
        private readonly LoggerInterface $messengerLogger,
    )
    {
    }
    
    public function __invoke(SomethingProcessed $message): void
    {
        $this->messengerLogger->warning('{method} processed', [
            'method' => __METHOD__,
        ]);
//        \dump(__METHOD__);
    }
}
