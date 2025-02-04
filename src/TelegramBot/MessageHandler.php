<?php

namespace App\TelegramBot;

use GrinWay\Telegram\Bot\Handler\Topic\Message\AbstractMessageHandler;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\Message\ChatMessage;

class MessageHandler extends AbstractMessageHandler
{
    protected function doDefaultHandle(ChatMessage $chatMessage, TelegramOptions $telegramOptions, mixed $fieldValue): bool
    {
        $chatMessage->subject('default message handler');
        return true;
    }
}
