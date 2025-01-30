<?php

namespace App\Test\KernelBrowser;

use Zenstruck\Browser\KernelBrowser;
use Zenstruck\Mailer\Test\Bridge\Zenstruck\Browser\MailerExtension;

// TODO: BaseKernelBrowser
/**
 * https://github.com/zenstruck/browser?tab=readme-ov-file#configuration
 * https://github.com/zenstruck/mailer-test
 */
class BaseKernelBrowser extends KernelBrowser
{
    use MailerExtension;
}
