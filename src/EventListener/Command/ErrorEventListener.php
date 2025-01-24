<?php

namespace App\EventListener\Command;

use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use function Symfony\Component\String\u;
use Symfony\Component\Validator\Exception\ValidationFailedException;

// TODO: ErrorEventListener (мне хотелось бы чтобы это работало даже когда не ошибка, а что-то типа ConsoleAskEvent)
/**
 * This console event listener allows you to normalize error message from Validation::createCallable()
 * Usage:
 *
 * $message = 'ONLY|Not blank|'; // pattern: ONLY|<YOUR MESSAGE>|
 *
 * $question->setMaxAttempts(1); // <- WON'T WORK WITHOUT IT
 * $question->setValidator(Validation::createCallable(
 *      new NotBlank(message: $message),
 * ));
 */
#[AsEventListener]
class ErrorEventListener
{
    public function __invoke(ConsoleErrorEvent $event)
    {
        \dump(__METHOD__);

        if ($event->getError() instanceof ValidationFailedException) {
            $message = $event->getError()->getMessage();
            $normalizedMessage = $this->getNormalizedMessage($message);

            if ($message !== $normalizedMessage) {
                $event->setError(new \Exception($normalizedMessage));
            }
        }
    }

    /**
     * @param string $getMessage
     * @return void
     */
    private function getNormalizedMessage(string $message)
    {
        if ([] !== $matches = u($message)->match('~ONLY\|(?<only>.+)\|~')) {
            $message = $matches['only'] ?? $message;
        }

        return $message;
    }
}
