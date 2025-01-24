<?php

namespace App\Telegram\Update\Handler\InlineQuery;

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

abstract class AbstractInlineQueryHandler extends AbstractTopicHandler
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

    abstract protected function doHandleInlineQuery(string $inlineQueryId, ChatMessage $chatMessage, TelegramOptions $telegramOptions, mixed $fieldValue): bool;

    public function supports(mixed $fieldValue): bool
    {
        return true;
    }

    protected function doHandle(ChatMessage $chatMessage, TelegramOptions $telegramOptions, mixed $fieldValue): bool
    {
        $inlineQueryId = $this->pa->getValue($fieldValue, '[id]');
        if (null === $inlineQueryId) {
            return false;
        }
        $inlineQueryId = (string)$inlineQueryId;

        return $this->doHandleInlineQuery($inlineQueryId, $chatMessage, $telegramOptions, $fieldValue);
    }
}
