<?php

namespace App\TelegramBot\Payment;

use GrinWay\Telegram\Bot\Handler\Topic\Payment\PreCheckoutQuery\AbstractPreCheckoutQueryHandler;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\Message\ChatMessage;

class BuyBtnPushed extends AbstractPreCheckoutQueryHandler
{
    protected function doPreCheckoutQueryHandle(string $preCheckoutQueryId, ChatMessage $chatMessage, TelegramOptions $telegramOptions, mixed $fieldValue): bool
    {
        $this->telegram->answerPreCheckoutQuery($preCheckoutQueryId, true);
        return false;
    }
}
