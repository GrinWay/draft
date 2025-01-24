<?php

namespace App\Telegram\Update\Handler\PriorityAble\Command\Message;

use App\Service\Telegram\Telegram;
use App\Service\Telegram\TelegramLabeledPrice;
use App\Service\Telegram\TelegramLabeledPrices;
use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class StartCommandHandler extends AbstractCommandHandler
{
    public const COMMAND_NAME = 'start';

    public function __construct(
        PropertyAccessorInterface                                           $pa,
        TranslatorInterface                                                 $t,
        Packages                                                            $asset,
        #[Autowire('%kernel.project_dir%')] string                          $projectDir,
        #[Autowire('%env(APP_TELEGRAM_BOT_NAME)%')] string                  $telegramBotName,
        Telegram                                                            $telegram,
        #[Autowire('%env(APP_TELEGRAM_Y_KASSA_API_TOKEN)%')] private string $telegramYKassaApiToken,
        ?ChatterInterface                                                   $chatter = null,
    )
    {
        parent::__construct(
            pa: $pa,
            t: $t,
            asset: $asset,
            projectDir: $projectDir,
            telegramBotName: $telegramBotName,
            telegram: $telegram,
            chatter: $chatter,
        );
    }

    protected function doCommandHandle(ChatMessage $chatMessage, TelegramOptions $telegramOptions, mixed $fieldValue): bool
    {
        $this->telegram->sendInvoice(
            chatId: $this->chatId,
            title: 'Порошок',
            description: '
Порошок хороший
Порошок, который смог',
            prices: new TelegramLabeledPrices(
                new TelegramLabeledPrice('Услуга', '100'),
//                new TelegramLabeledPrice('Услуга менее рубля', '099'),
            ),
            providerToken: $this->telegramYKassaApiToken,
            currency: 'RUB',
            photoUri: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSM-iiLxH9z4gHSVL0oq0p_fn6FvpqO2jSdpw&s',
            needShippingAddress: true,
            isFlexible: true,
//            throw: true,
//            needName: true,
//            needPhoneNumber: true,
//            needEmail: true,
//            sendEmailToProvider: true,
        );

//        $chatMessage->subject('<i>Start command menu</i>');
//        $telegramOptions = $telegramOptions
//            ->replyMarkup((new InlineKeyboardMarkup())
//                ->inlineKeyboard([
//                    (new InlineKeyboardButton($v = 'Do 1'))->callbackData(InlineKeyboardButtonType::START_COMMAND_PREFIX . $v),
//                    (new InlineKeyboardButton($v = 'Do 2'))->callbackData(InlineKeyboardButtonType::START_COMMAND_PREFIX . $v),
//                ])
//            )//
//        ;
        return false;
    }
}
