<?php

namespace App\Messenger\Command\ProcessSomething;

use Symfony\Component\Validator\Constraints as Assert;

class ProcessSomethingMessage
{
    public string $name = '';

    public function __construct()
    {
    }
}
