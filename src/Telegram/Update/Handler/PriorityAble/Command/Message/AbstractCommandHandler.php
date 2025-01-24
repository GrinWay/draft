<?php

namespace App\Telegram\Update\Handler\PriorityAble\Command\Message;

use App\Telegram\Update\Handler\PriorityAble\AbstractMessageTopicHandler;
use App\Type\Telegram\InlineKeyboardButtonType;
use Symfony\Component\Notifier\Bridge\Telegram\Reply\Markup\Button\InlineKeyboardButton;
use Symfony\Component\Notifier\Bridge\Telegram\Reply\Markup\InlineKeyboardMarkup;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\Message\ChatMessage;

abstract class AbstractCommandHandler extends AbstractMessageTopicHandler
{
    public const COMMAND_NAME = '!CHANGE_ME!';

    abstract protected function doCommandHandle(ChatMessage $chatMessage, TelegramOptions $telegramOptions, mixed $fieldValue): bool;

    private static function getCommandName(): string
    {
        return static::COMMAND_NAME;
    }

    public function supports(mixed $fieldValue): bool
    {
        return parent::supports($fieldValue) && \is_string($this->text) && \str_starts_with($this->text, \sprintf('/%s', static::getCommandName()));
    }

    protected function doHandle(ChatMessage $chatMessage, TelegramOptions $telegramOptions, mixed $fieldValue): bool
    {
        if ($this->alwaysAcceptFullCommandVersion($fieldValue) || $this->acceptShortVersionOnlyIfPrivateChat($fieldValue)) {
            return $this->doCommandHandle($chatMessage, $telegramOptions, $fieldValue);
        }
        return false;
    }

    private function alwaysAcceptFullCommandVersion(mixed $fieldValue): bool
    {
        return \str_starts_with($this->text, \sprintf('/%s@%s', static::getCommandName(), $this->telegramBotName));
    }

    private function acceptShortVersionOnlyIfPrivateChat(mixed $fieldValue): bool
    {
        return \str_starts_with($this->text, \sprintf('/%s', static::getCommandName()))
            && $this->pa->getValue($fieldValue, '[chat][id]') === $this->pa->getValue($fieldValue, '[from][id]');
    }
}
