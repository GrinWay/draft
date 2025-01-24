<?php

namespace App\Telegram\Update\Handler;

use App\Service\Telegram\Telegram;
use App\Telegram\Contract\TopicHandlerInterface;
use App\Telegram\Contract\UpdateHandlerInterface;
use App\Trait\Telegram\TelegramAwareTrait;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractUpdateHandler implements UpdateHandlerInterface
{
    use TelegramAwareTrait;

    protected PropertyAccessorInterface $pa;
    protected $handlers;
    protected Telegram $telegram;

    #[Required]
    public function _setRequirements(
        PropertyAccessorInterface $pa,
        Telegram                  $telegram,
    )
    {
        $this->pa = $pa;
        $this->telegram = $telegram;
    }

    public static function updateField(): string
    {
        return static::UPDATE_FIELD;
    }

    protected function getTopicHandlers(): iterable
    {
        return $this->telegram->getUpdateHandlerIterator($this->getUpdateHandlerKey(static::updateField()));
    }

    public function isOptional(): bool
    {
        return true;
    }

    public function supports(mixed $fieldValue): bool
    {
        return true;
    }

    public function handle(mixed $fieldValue): void
    {
        foreach ($this->getTopicHandlers() as $handler) {
            \assert($handler instanceof TopicHandlerInterface);

            $handler->beforeSupports($fieldValue);
            if ($handler->supports($fieldValue)) {
                $handler->handle($fieldValue);
                break;
            }
        }
    }
}
