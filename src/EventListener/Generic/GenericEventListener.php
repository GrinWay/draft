<?php

namespace App\EventListener\Generic;

use App\Messenger\Command\ProcessSomething\ProcessSomethingMessage;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\GenericEvent;

#[AsEventListener(GenericEvent::class, '__invoke')]
class GenericEventListener
{
    public function __invoke(GenericEvent $event)
    {
        $subject = $event->getSubject();
        $args = $event->getArguments();

        if ($subject instanceof ProcessSomethingMessage) {
            $this->processSomethingEventListener($subject);
            return;
        }
    }

    /**
     * @param ProcessSomethingMessage $subject
     * @return void
     */
    private function processSomethingEventListener(ProcessSomethingMessage $subject): void
    {
        return;
        $subject->someDynamicProp = 'modification from event listener';
    }
}
