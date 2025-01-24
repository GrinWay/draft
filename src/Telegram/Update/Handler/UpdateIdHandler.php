<?php

namespace App\Telegram\Update\Handler;

class UpdateIdHandler extends AbstractUpdateHandler
{
    public const UPDATE_FIELD = 'update_id';

    public function isOptional(): bool
    {
        return false;
    }
}
