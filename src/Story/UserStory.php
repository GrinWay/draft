<?php

namespace App\Story;

use App\Factory\FoundryFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class UserStory extends Story
{
    public function build(): void
    {
        $this->addState(
            'firstTen',
            UserFactory::new()
                ->many(10)
                ->create()
        );
    }
}
