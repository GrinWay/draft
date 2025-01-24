<?php

namespace App\Test\DataProvider;

class RouterProvider
{
    public static function all()
    {
        return [
            'Home' => ['/'],
            'Messenger' => ['/messenger'],
        ];
    }
}
