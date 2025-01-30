<?php

namespace App\Tests\Functional\Controller;

use App\Factory\TodoFactory;
use App\Test\DataProvider\RouterProvider;
use App\Tests\Functional\AbstractWebTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Mailer\Test\InteractsWithMailer;
use Zenstruck\Mailer\Test\TestEmail;

#[CoversNothing]
class EmailControllerTest extends AbstractWebTestCase
{
    use Factories, HasBrowser, ResetDatabase, InteractsWithMailer;

    private string $appTestEmail;
    private string $appAdminEmail;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $container = self::getContainer();
        $getenv = $container->get('container.getenv');
        $this->appTestEmail = $getenv('APP_TEST_EMAIL');
        $this->appAdminEmail = $getenv('APP_ADMIN_EMAIL');
        self::ensureKernelShutdown();
    }

    public function testOneEmailSent()
    {
        $appAdminEmail = $this->appAdminEmail;

        $this->browser()
            ->withProfiling()
            ->visit('/email/test')
            ->assertSuccessful()
            ->assertSentEmailCount(1)
            ->assertEmailSentTo($this->appTestEmail, static function (TestEmail $email) use ($appAdminEmail) {
                $email
                    ->assertFrom($appAdminEmail)
                    ->assertSubject('[TEST] TEST')//
                ;
            })//
        ;

        $this->mailer()->reset();
    }
}
