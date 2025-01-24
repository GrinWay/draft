<?php

namespace App\Telegram\Update\Handler;

class DeletedBusinessMessagesHandler extends AbstractUpdateHandler
{
    public const UPDATE_FIELD = 'deleted_business_messages';
}
