<?php

namespace App\Telegram\Update\Handler\InlineQuery;

use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\Message\ChatMessage;

class DefaultInlineQueryHandler extends AbstractInlineQueryHandler
{
    protected function doHandleInlineQuery(string $inlineQueryId, ChatMessage $chatMessage, TelegramOptions $telegramOptions, mixed $fieldValue): bool
    {
        $this->telegram->answerInlineQuery($inlineQueryId, 'gif', [
            'gif_url' => 'https://media1.giphy.com/media/a93jwI0wkWTQs/giphy.gif',
            'thumbnail_url' => 'https://media1.giphy.com/media/a93jwI0wkWTQs/giphy.gif',
        ]);
        return true;
    }
}
