<?php

namespace App\Telegram\Update\Handler\PriorityAble\Group\Message;

use App\Telegram\Update\Handler\PriorityAble\AbstractMessageTopicHandler;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\Message\ChatMessage;

class VoiceMessageHandler extends AbstractMessageTopicHandler
{
    private ?string $voiceFieldId = null;

    public function supports(mixed $fieldValue): bool
    {
        return parent::supports($fieldValue)
            && null !== $this->voiceFieldId = $this->pa->getValue($fieldValue, '[voice][file_id]');
    }

    protected function doHandle(ChatMessage $chatMessage, TelegramOptions $telegramOptions, mixed $fieldValue): bool
    {
        \dump('VOICE', $this->voiceFieldId);
        return false;
    }
}
