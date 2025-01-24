<?php

namespace App\Service;

use App\Service\Telegram\Telegram;

// TODO: FiguresRepresentation
class FiguresRepresentation
{
    /**
     * This method joins number with end figures
     * @return string
     */
    public static function concatNumbersWithCorrectCountOfEndFigures(int $startNumber, int $endNumber, int $countEndFigures = Telegram::LENGTH_AMOUNT_END_FIGURES): string
    {
        $endFigures = (string)$endNumber;
        $endFiguresLen = \strlen($endFigures);
        if ($endFiguresLen < $countEndFigures) {
            $diff = $countEndFigures - $endFiguresLen;
            $endFigures = \str_repeat('0', $diff) . $endFigures;
        }
        return $startNumber . $endFigures;
    }
}
