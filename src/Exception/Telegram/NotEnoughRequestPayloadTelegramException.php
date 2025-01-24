<?php

namespace App\Exception\Telegram;

use JetBrains\PhpStorm\Internal\LanguageLevelTypeAware;

class NotEnoughRequestPayloadTelegramException extends AbstractTelegramException
{
    public function __construct(
        private readonly array                                                       $requiredRequestPayloadKeys,
        #[LanguageLevelTypeAware(['8.0' => 'int'], default: '')]                     $code = 0,
        #[LanguageLevelTypeAware(['8.0' => 'Throwable|null'], default: 'Throwable')] $previous = null
    )
    {
        $message = \sprintf('Not enough request payload, required: "%s"', implode('", "', $requiredRequestPayloadKeys));

        parent::__construct(
            message: $message,
            code: $code,
            previous: $previous,
        );
    }
}
