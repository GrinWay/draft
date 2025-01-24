<?php

namespace App\Telegram\Update\Handler\CallbackQuery\Command;

use App\Telegram\Update\Handler\CallbackQuery\AbstractCallbackQueryHandler;
use App\Type\Telegram\InlineKeyboardButtonType;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\Message\ChatMessage;

/**
 * When inline button was pushed
 */
class StartCommandInlineButtonHandler extends AbstractCallbackQueryHandler
{
    protected ?string $buttonData = null;
    protected string|null $subject = null;

    public function supports(mixed $fieldValue): bool
    {
        $this->subject = $this->pa->getValue($fieldValue, '[message][text]');
        $this->buttonData = $this->pa->getValue($fieldValue, '[data]');

        return null !== $this->buttonData && \str_starts_with($this->buttonData, InlineKeyboardButtonType::START_COMMAND_PREFIX);
    }

    protected function doCallbackQueryHandle(ChatMessage $chatMessage, TelegramOptions $telegramOptions, mixed $fieldValue): bool
    {
        $chatMessage->subject(\sprintf('Ответ на нажатие кнопки команды /start %s', ''));
        $telegramOptions
            ->chatId($this->chatId)
            ->answerCallbackQuery(
                callbackQueryId: $this->callbackQueryId,
                showAlert: true,
            );
        return true;
    }
}
