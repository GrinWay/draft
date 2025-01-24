<?php

namespace App\Telegram\Update\Handler\Message;

use App\Telegram\Update\Handler\PriorityAble\AbstractMessageTopicHandler;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\Message\ChatMessage;

abstract class AbstractDefaultMessageHandler extends AbstractMessageTopicHandler
{
    abstract protected function doDefaultHandle(ChatMessage $chatMessage, TelegramOptions $telegramOptions, mixed $fieldValue): bool;

    protected function doHandle(ChatMessage $chatMessage, TelegramOptions $telegramOptions, mixed $fieldValue): bool
    {
        return $this->doDefaultHandle($chatMessage, $telegramOptions, $fieldValue);
    }
}
