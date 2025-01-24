<?php

namespace App\Validator\Telegram;

use Symfony\Component\Validator\Constraint;

// TODO: TelegramAmountWithEndFigures
/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class TelegramAmountWithEndFigures extends Constraint
{
    public string $message = 'The value "{{ amount }}" is not valid telegram amount representation, valid is 100 for 1.00';
}
