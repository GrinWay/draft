<?php

namespace App\Test\DataProvider;

class HomeControllerDataProvider
{
    public static function titles(): array
    {
        $title = 'Home';
        return [
            $title => [$title],
        ];
    }
}
