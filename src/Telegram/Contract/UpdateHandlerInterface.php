<?php

namespace App\Telegram\Contract;

/**
 * https://core.telegram.org/bots/api#update
 */
interface UpdateHandlerInterface
{
    public function isOptional(): bool;

    /**
     * If update filed existence depends on other keys existence use it
     *
     * @param mixed $fieldValue
     * @return bool
     */
    public function supports(mixed $fieldValue): bool;

    public static function updateField(): string;

    public function handle(mixed $fieldValue): void;
}
