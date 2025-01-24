<?php

namespace App\Test\Component;

use Zenstruck\Browser\Component;

class VisitComponent extends Component
{
    public function home(): self
    {
        $this->browser()->visit('/');

        return $this;
    }
}
