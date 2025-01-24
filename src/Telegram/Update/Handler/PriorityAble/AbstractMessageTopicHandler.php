<?php

namespace App\Telegram\Update\Handler\PriorityAble;

use App\Telegram\Update\Handler\AbstractTopicHandler;

abstract class AbstractMessageTopicHandler extends AbstractTopicHandler
{
    public function supports(mixed $fieldValue): bool
    {
        return null !== $this->chatId;
    }
}
