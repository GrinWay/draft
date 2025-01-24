<?php

namespace App\Test\KernelBrowser;

use Zenstruck\Browser\KernelBrowser;

class BaseKernelBrowser extends KernelBrowser
{
    /**
     * Alias
     * @return void
     */
    public function assertIsSuccessful(): static
    {
        $this->assertSuccessful();
        return $this;
    }
}
