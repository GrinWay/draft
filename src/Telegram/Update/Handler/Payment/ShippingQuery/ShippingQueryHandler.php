<?php

namespace App\Telegram\Update\Handler\Payment\ShippingQuery;

use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\Message\ChatMessage;

class ShippingQueryHandler extends AbstractShippingQueryHandler
{
    protected function doHandleShippingQuery(string $shippingQueryId, ChatMessage $chatMessage, TelegramOptions $telegramOptions, mixed $fieldValue): bool
    {
        \dump($fieldValue);

        $countryCode = $this->pa->getValue($fieldValue, '[shipping_address][country_code]');
        $state = $this->pa->getValue($fieldValue, '[shipping_address][state]');
        $city = $this->pa->getValue($fieldValue, '[shipping_address][city]');
        $postCode = $this->pa->getValue($fieldValue, '[shipping_address][post_code]');

        $shippingOptions = [
            [
                'id' => '1',
                'title' => 'Майами битч',
                'prices' => [
                    [
                        'label' => 'label 1',
                        'amount' => '10000',
                    ],
                    [
                        'label' => 'label 2',
                        'amount' => '20000',
                    ],
                ],
            ],
            [
                'id' => '2',
                'title' => 'Майами битч 2',
                'prices' => [
                    [
                        'label' => 'label 21',
                        'amount' => '100000',
                    ],
                    [
                        'label' => 'label 22',
                        'amount' => '200000',
                    ],
                ],
            ],
        ];
        $shippingQueryIsValid = 'Не сможем доставить никаких товаров по указанному адресу';
        $shippingQueryIsValid = true;

        $this->telegram->answerShippingQuery($shippingQueryId, $shippingOptions, $shippingQueryIsValid);
        return false;
    }
}
