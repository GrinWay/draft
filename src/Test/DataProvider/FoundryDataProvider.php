<?php

namespace App\Test\DataProvider;

use App\Factory\FoundryFactory;

class FoundryDataProvider
{
    public static function foundries(): iterable
    {
        return [
            'Foundry Factory' => [FoundryFactory::new()]
        ];
    }
}
