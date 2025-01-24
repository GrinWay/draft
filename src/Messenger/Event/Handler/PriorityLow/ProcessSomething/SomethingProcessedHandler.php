<?php

namespace App\Messenger\Event\Handler\PriorityLow\ProcessSomething;

use App\Messenger\Event\ProcessSomething\SomethingProcessed;

class SomethingProcessedHandler
{
    public function __invoke(SomethingProcessed $message): void
    {
//        \dump(__METHOD__);
    }
}
