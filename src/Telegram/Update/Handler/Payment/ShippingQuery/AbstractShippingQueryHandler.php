<?php

namespace App\Telegram\Update\Handler\Payment\ShippingQuery;

use App\Service\Telegram\Telegram;
use App\Telegram\Update\Handler\AbstractTopicHandler;
use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * https://core.telegram.org/bots/api#answershippingquery
 */
abstract class AbstractShippingQueryHandler extends AbstractTopicHandler
{
    public function __construct(
        PropertyAccessorInterface                          $pa,
        TranslatorInterface                                $t,
        Packages                                           $asset,
        #[Autowire('%kernel.project_dir%')] string         $projectDir,
        #[Autowire('%env(APP_TELEGRAM_BOT_NAME)%')] string $telegramBotName,
        Telegram                                           $telegram,
        //
        protected readonly SerializerInterface             $serializer,
        protected readonly HttpClientInterface             $telegramClient,
        ?ChatterInterface                                  $chatter = null,
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

    /**
     * We're here when Telegram::sendInvoice(..., needShippingAddress: true, isFlexible: true)
     */
    abstract protected function doHandleShippingQuery(string $shippingQueryId, ChatMessage $chatMessage, TelegramOptions $telegramOptions, mixed $fieldValue): bool;

    public function supports(mixed $fieldValue): bool
    {
        return true;
    }

    protected function doHandle(ChatMessage $chatMessage, TelegramOptions $telegramOptions, mixed $fieldValue): bool
    {
        $shippingQueryId = $this->pa->getValue($fieldValue, '[id]');
        if (null === $shippingQueryId) {
            return false;
        }
        $shippingQueryId = (string)$shippingQueryId;

        return $this->doHandleShippingQuery($shippingQueryId, $chatMessage, $telegramOptions, $fieldValue);
    }
}
