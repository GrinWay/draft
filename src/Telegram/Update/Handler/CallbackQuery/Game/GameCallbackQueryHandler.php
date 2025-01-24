<?php

namespace App\Telegram\Update\Handler\CallbackQuery\Game;

use App\Telegram\Update\Handler\CallbackQuery\AbstractCallbackQueryHandler;
use App\Type\Telegram\InlineKeyboardButtonType;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\VarExporter\Hydrator;

/**
 * When inline button was pushed
 */
class GameCallbackQueryHandler extends AbstractCallbackQueryHandler
{
    private ?string $gameShortName = null;

    public function supports(mixed $fieldValue): bool
    {
        return null !== $this->gameShortName = $this->pa->getValue($fieldValue, '[game_short_name]');
    }

    protected function doCallbackQueryHandle(ChatMessage $chatMessage, TelegramOptions $telegramOptions, mixed $fieldValue): bool
    {
        $chatMessage->subject('GAME...');
        Hydrator::hydrate($telegramOptions, [
            'options' => [
                ...$telegramOptions->toArray(),
                'url' => 'https://play.famobi.com/wrapper/om-nom-run/A1000-10',
            ],
        ]);
        $telegramOptions
            ->chatId($this->chatId)
            ->answerCallbackQuery(
                callbackQueryId: $this->callbackQueryId,
                showAlert: true,
            );
        return true;
    }
}
