<?php

namespace App\Telegram\Update\Handler;

use App\Service\Telegram\Telegram;
use App\Telegram\Contract\TopicHandlerInterface;
use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Message\SentMessage;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\VarExporter\Hydrator;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractTopicHandler implements TopicHandlerInterface
{
    // Not required for inline query
    protected ?string $chatId = null;
    protected ?string $text = null;

    public function __construct(
        protected PropertyAccessorInterface                          $pa,
        protected TranslatorInterface                                $t,
        protected Packages                                           $asset,
        #[Autowire('%kernel.project_dir%')] protected string         $projectDir,
        #[Autowire('%env(APP_TELEGRAM_BOT_NAME)%')] protected string $telegramBotName,
        protected Telegram                                           $telegram,
        protected ?ChatterInterface                                  $chatter = null,
    )
    {
    }

    public function beforeSupports(mixed $fieldValue): static
    {
        $this->setChatId($fieldValue);
        $this->setText($fieldValue);
        return $this;
    }

    /**
     * You must explicitly $chatMessage->subject('NOT EMPTY CONTENT')
     *
     * @param ChatMessage $chatMessage
     * @param TelegramOptions $telegramOptions
     * @param mixed $fieldValue
     * @return bool If false there will be NO response (but you supported that NO response)
     */
    abstract protected function doHandle(ChatMessage $chatMessage, TelegramOptions $telegramOptions, mixed $fieldValue): bool;

    public function handleSentMessage(?SentMessage $sentMessage): void
    {
//        \dump($sentMessage);
    }

    public function handle(mixed $fieldValue): void
    {
        $chatMessage = new ChatMessage('');
        $telegramOptions = (new TelegramOptions())
            ->parseMode(TelegramOptions::PARSE_MODE_HTML)//
        ;
        $this->configureTelegramOptions($telegramOptions, $fieldValue);
        $isHandleable = $this->doHandle($chatMessage, $telegramOptions, $fieldValue);

        if ('' === $chatMessage->getSubject() || false === $isHandleable) {
            return;
        }

        // bad (у callback_query другая структура)
        if (true === $this->pa->getValue($fieldValue, '[is_topic_message]')
            || true === $this->pa->getValue($fieldValue, '[message][is_topic_message]')) {
            $topicId = $this->pa->getValue($fieldValue, '[message_thread_id]') ?? $this->pa->getValue($fieldValue, '[message][message_thread_id]');
            if ($topicId) {
                // cuz of there is no such a method I have to do this masochistic actions
//                $telegramOptions->messageThreadId($topicId);
                Hydrator::hydrate($telegramOptions, [
                    'options' => [
                        ...$telegramOptions->toArray(),
                        'message_thread_id' => $topicId,
                    ],
                ]);
            }
        }

        $chatMessage->options($telegramOptions);
        $sentMessage = $this->chatter?->send($chatMessage);

        $this->handleSentMessage($sentMessage);
    }

    private function configureTelegramOptions(TelegramOptions $telegramOptions, mixed $fieldValue): void
    {
        if (null !== $this->chatId) {
            $telegramOptions->chatId($this->chatId);
        }
    }

    private function setChatId(mixed $fieldValue): void
    {
        if (!\is_array($fieldValue)) {
            return;
        }
        $chatId = $this->pa->getValue($fieldValue, '[chat][id]');
        if (null === $chatId) {
            $chatId = $this->pa->getValue($fieldValue, '[message][chat][id]');
        }
        $this->chatId = $chatId;
    }

    private function setText(mixed $fieldValue)
    {
        $this->text = $this->pa->getValue($fieldValue, '[text]');
    }
}
