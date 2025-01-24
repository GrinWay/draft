<?php

namespace App\Telegram\Update\Handler\PriorityAble\Command\Message;

use App\Telegram\Update\Handler\PriorityAble\AbstractMessageTopicHandler;
use App\Type\Telegram\InlineKeyboardButtonType;
use Symfony\Component\Notifier\Bridge\Telegram\Reply\Markup\Button\InlineKeyboardButton;
use Symfony\Component\Notifier\Bridge\Telegram\Reply\Markup\InlineKeyboardMarkup;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\Message\ChatMessage;

class TermsCommandHandler extends AbstractCommandHandler
{
    public const COMMAND_NAME = 'terms';

    protected function doCommandHandle(ChatMessage $chatMessage, TelegramOptions $telegramOptions, mixed $fieldValue): bool
    {
        $subject = <<<'SUBJECT_TEXT'
Тут будут описаны правила использования бота
SUBJECT_TEXT;
        $chatMessage->subject($subject);
        return true;
    }
}
