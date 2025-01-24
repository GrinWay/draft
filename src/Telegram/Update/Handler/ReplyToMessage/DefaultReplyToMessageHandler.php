<?php

namespace App\Telegram\Update\Handler\ReplyToMessage;

use App\Telegram\Update\Handler\AbstractTopicHandler;
use App\Telegram\Update\Handler\PriorityAble\AbstractMessageTopicHandler;
use Symfony\Component\Notifier\Bridge\Telegram\Reply\Markup\Button\InlineKeyboardButton;
use Symfony\Component\Notifier\Bridge\Telegram\Reply\Markup\Button\KeyboardButton;
use Symfony\Component\Notifier\Bridge\Telegram\Reply\Markup\InlineKeyboardMarkup;
use Symfony\Component\Notifier\Bridge\Telegram\Reply\Markup\ReplyKeyboardMarkup;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\Message\ChatMessage;

class DefaultReplyToMessageHandler extends AbstractReplyToMessageHandler
{
    protected function doHandle(ChatMessage $chatMessage, TelegramOptions $telegramOptions, mixed $fieldValue): bool
    {
        $chatMessage->subject(\sprintf('Reply: "%s" to a message: "%s"', $this->replyText, $this->replyToMessageText));

        $telegramOptions = $telegramOptions
            ->disableWebPagePreview(true)
            ->disableNotification(true)//
        ;
        return true;
    }
}
