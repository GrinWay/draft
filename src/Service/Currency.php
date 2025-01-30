<?php

namespace App\Service;

/**
 * Telegram amount style explanation: (IMAGINED 1.00 becomes REPRESENTED IN VARIABLE AS 100)
 */
class Currency
{
    // TODO: realize (Currency)
    /**
     * @param string $amountWithEndFigures Telegram amount style
     * @return string Telegram amount style
     */
    public function transferAmountFromTo(string $amountWithEndFigures, string $amountCurrency, mixed $convertToCurrency): string
    {
        return '20000';
    }
}
