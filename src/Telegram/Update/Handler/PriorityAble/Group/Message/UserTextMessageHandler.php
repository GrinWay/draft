<?php

namespace App\Telegram\Update\Handler\PriorityAble\Group\Message;

use App\Telegram\Update\Handler\PriorityAble\AbstractMessageTopicHandler;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\Message\ChatMessage;

class UserTextMessageHandler extends AbstractMessageTopicHandler
{
    public function supports(mixed $fieldValue): bool
    {
        return parent::supports($fieldValue)
            && null !== $this->text;
    }

    protected function doHandle(ChatMessage $chatMessage, TelegramOptions $telegramOptions, mixed $fieldValue): bool
    {
        $userId = $this->pa->getValue($fieldValue, '[from][id]');
        $chatMessage->subject(\sprintf('Group message'));

        $telegramOptions = $telegramOptions
            ->disableWebPagePreview(true)
            ->disableNotification(true)//
        ;
        return true;
    }
}
