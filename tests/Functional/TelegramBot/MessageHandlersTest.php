<?php

namespace App\Tests\Functional\TelegramBot;

use App\Controller\TelegramController;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\Recipient;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

#[CoversClass(TelegramController::class)]
class MessageHandlersTest extends AbstractTelegramTestCase
{
    use Factories, HasBrowser, ResetDatabase;

//    public function testMessageHandlerWithTelegramClient()
//    {
//        $response = $this->telegramClient->request('POST', 'sendMessage', [
//            'json' => [
//                'chat_id' => $this->getTestTelegramChatId(),
//                'text' => 'TEST TEXT',
//            ],
//        ]);
//
//        $response = $this->decodeResponse($response);
//        $ok = $this->pa->getValue($response, '[ok]');
//
//        $this->assertSame(true, $ok);
//    }
}
