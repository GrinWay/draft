<?php

namespace App\Telegram\Update\Handler\Payment\PreCheckoutQuery;

use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\Message\ChatMessage;

class PreCheckoutQueryHandler extends AbstractPreCheckoutQueryHandler
{
    protected function doHandlePreCheckoutQuery(string $preCheckoutQueryId, ChatMessage $chatMessage, TelegramOptions $telegramOptions, mixed $fieldValue): bool
    {
        $invoicePayload = $this->pa->getValue($fieldValue, '[invoice_payload]');

        $preCheckoutQueryIsValid = $this->isPreCheckoutQueryValid($fieldValue);
        \dump($preCheckoutQueryIsValid);
        $this->telegram->answerPreCheckoutQuery(
            $preCheckoutQueryId,
            $preCheckoutQueryIsValid,
        );
        return false;
    }

    private function isPreCheckoutQueryValid(mixed $fieldValue): true|string
    {
        $fromBot = $this->pa->getValue($fieldValue, '[from][is_bot]');
        if (true === $fromBot) {
            return 'Боты не могут оплачивать услуги';
        }

        $email = $this->pa->getValue($fieldValue, '[order_info][email]');
        $phoneNumber = $this->pa->getValue($fieldValue, '[order_info][phone_number]');
        $shippingAddress = $this->pa->getValue($fieldValue, '[order_info][shipping_address]');

        return true;
    }
}
