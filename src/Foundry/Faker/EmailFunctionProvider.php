<?php

namespace App\Foundry\Faker;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use function Zenstruck\Foundry\faker;

#[AutoconfigureTag('foundry.faker_provider')]
class EmailFunctionProvider
{
    public function testEmail()
    {
        return \preg_replace('~^(.+)@(.+)$~', '\1@fake.test', faker()->email());
    }
}
