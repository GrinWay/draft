<?php

namespace App\Story;

use App\Factory\FoundryFactory;
use Zenstruck\Foundry\Story;

final class FoundryStory extends Story
{
    public function build(): void
    {
        $this->addState(
            'firstTen',
            FoundryFactory::new()
                ->many(10)
                ->create(static fn($i) => ['title' => \sprintf('TEST title %s', $i)])
        );
    }
}
