<?php

namespace App\Telegram\Update\Handler\PriorityAble\Command\Message\LowPriority;

use App\Telegram\Update\Handler\PriorityAble\Command\Message\AbstractCommandHandler;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\Message\ChatMessage;

/**
 * For caching incorrect commands
 *
 * Supports but not handles (null answer to incorrect command names)
 */
class NullResponseToIncorrectCommandHandler extends AbstractCommandHandler
{
    public function supports(mixed $fieldValue): bool
    {
        return \is_string($this->text) && \str_starts_with($this->text, '/');
    }

    protected function doCommandHandle(ChatMessage $chatMessage, TelegramOptions $telegramOptions, mixed $fieldValue): bool
    {
        return false;
    }
}
