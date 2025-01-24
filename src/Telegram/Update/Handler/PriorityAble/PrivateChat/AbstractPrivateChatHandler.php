<?php

namespace App\Telegram\Update\Handler\PriorityAble\PrivateChat;

use App\Telegram\Update\Handler\PriorityAble\AbstractMessageTopicHandler;

abstract class AbstractPrivateChatHandler extends AbstractMessageTopicHandler
{
    public function supports(mixed $fieldValue): bool
    {
        return parent::supports($fieldValue)
            && null !== $this->text
            && $this->pa->getValue($fieldValue, '[chat][id]') === $this->pa->getValue($fieldValue, '[from][id]');
    }
}
