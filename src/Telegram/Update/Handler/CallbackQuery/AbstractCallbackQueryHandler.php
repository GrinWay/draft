<?php

namespace App\Telegram\Update\Handler\CallbackQuery;

use App\Telegram\Update\Handler\AbstractTopicHandler;
use App\Type\Telegram\InlineKeyboardButtonType;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\Message\ChatMessage;

/**
 * When inline button was pushed
 */
abstract class AbstractCallbackQueryHandler extends AbstractTopicHandler
{
    protected string|int|null $callbackQueryId = null;

    abstract protected function doCallbackQueryHandle(ChatMessage $chatMessage, TelegramOptions $telegramOptions, mixed $fieldValue): bool;

    protected function doHandle(ChatMessage $chatMessage, TelegramOptions $telegramOptions, mixed $fieldValue): bool
    {
        $this->callbackQueryId = $this->pa->getValue($fieldValue, '[id]');

        if (null === $this->chatId || null === $this->callbackQueryId) {
            return false;
        }

        return $this->doCallbackQueryHandle($chatMessage, $telegramOptions, $fieldValue);
    }
}
