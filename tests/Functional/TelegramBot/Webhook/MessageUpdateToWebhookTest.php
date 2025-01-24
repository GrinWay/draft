<?php

namespace App\Tests\Functional\TelegramBot\Webhook;

use App\Controller\TelegramController;
use App\Tests\Functional\TelegramBot\AbstractTelegramTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

#[CoversClass(TelegramController::class)]
class MessageUpdateToWebhookTest extends AbstractTelegramTestCase
{
    use Factories, HasBrowser, ResetDatabase;

    public function testMessageUpdate()
    {
        $profile = $this->postRequestTelegramWebhook($this->getMessageUpdatePayload());
        $httpClientDataCollector = $profile?->getCollector('http_client');
//        $messengerDataCollector = $profile?->getCollector('messenger');
//        $notifierDataCollector = $profile?->getCollector('notifier');
//        $requestDataCollector = $profile?->getCollector('request');

        $this->assertSame(0, $httpClientDataCollector?->getErrorCount());
        $this->assertNotificationCount(1);
    }
}
