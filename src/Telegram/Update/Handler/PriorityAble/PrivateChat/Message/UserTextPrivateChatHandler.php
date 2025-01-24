<?php

namespace App\Telegram\Update\Handler\PriorityAble\PrivateChat\Message;

use App\Telegram\Update\Handler\PriorityAble\PrivateChat\AbstractPrivateChatHandler;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\Message\ChatMessage;

class UserTextPrivateChatHandler extends AbstractPrivateChatHandler
{
    protected function doHandle(ChatMessage $chatMessage, TelegramOptions $telegramOptions, mixed $fieldValue): bool
    {
        $chatMessage->subject(\sprintf('Private message to: "%s"', $this->text));
        return true;
    }
}
