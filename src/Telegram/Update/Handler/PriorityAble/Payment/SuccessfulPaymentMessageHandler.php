<?php

namespace App\Telegram\Update\Handler\PriorityAble\Payment;

use App\Telegram\Update\Handler\PriorityAble\AbstractMessageTopicHandler;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\Message\ChatMessage;

class SuccessfulPaymentMessageHandler extends AbstractMessageTopicHandler
{
    private ?array $successfulPayment = null;

    public function supports(mixed $fieldValue): bool
    {
        return parent::supports($fieldValue)
            && (null !== $this->successfulPayment = $this->pa->getValue($fieldValue, '[successful_payment]'));
    }

    protected function doHandle(ChatMessage $chatMessage, TelegramOptions $telegramOptions, mixed $fieldValue): bool
    {
        $providerPaymentChargeId = $this->pa->getValue($this->successfulPayment, '[provider_payment_charge_id]');

        \dump('successful_payment', $this->successfulPayment, $providerPaymentChargeId);
        $chatMessage->subject(\sprintf('PAYMENT IS SUCCESSFUL'));
        return true;
    }
}
