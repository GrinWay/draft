<?php

namespace App\Telegram\Update\Handler\ReplyToMessage;

use App\Telegram\Update\Handler\AbstractTopicHandler;

abstract class AbstractReplyToMessageHandler extends AbstractTopicHandler
{
    protected ?string $replyText = null;
    protected ?string $replyToMessageText = null;

    public function supports(mixed $fieldValue): bool
    {
        return null !== $this->chatId && null !== ($this->replyText = $this->pa->getValue($fieldValue, '[text]')) && null !== ($this->replyToMessageText = $this->pa->getValue($fieldValue, '[reply_to_message][text]'));
    }
}
