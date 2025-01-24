<?php

namespace App\Type\Telegram;

class InlineKeyboardButtonType
{
    public const COMMAND_PREFIX = 'command_';
    public const START_COMMAND_PREFIX = self::COMMAND_PREFIX . 'start_';
    public const TERMS_COMMAND_PREFIX = self::COMMAND_PREFIX . 'terms_';
}
