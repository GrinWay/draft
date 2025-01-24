<?php

namespace App\Trait\Telegram;

trait TelegramAwareTrait
{
    protected function getUpdateHandlerKey(string $topic): string
    {
        return \sprintf('grinway.telegram_%s_handlers', $topic);
    }
}
