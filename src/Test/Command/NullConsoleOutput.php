<?php

namespace App\Test\Command;

use Symfony\Component\Console\Formatter\NullOutputFormatter;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

class NullConsoleOutput extends NullOutput implements ConsoleOutputInterface
{
    private array $sections = [];

    public function __construct()
    {
    }

    public function getErrorOutput(): OutputInterface
    {
        return new NullOutput();
    }

    public function setErrorOutput(OutputInterface $error): void
    {
    }

    public function section(): ConsoleSectionOutput
    {
        return new ConsoleSectionOutput(
            \fopen('php://memory', 'r+'),
            $this->sections,
            0,
            false,
            new NullOutputFormatter(),
        );
    }
}
