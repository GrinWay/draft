<?php

namespace App\Tests\Functional\TelegramBot;

use App\Tests\Functional\AbstractWebTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpClient\DataCollector\HttpClientDataCollector;
use Symfony\Component\HttpKernel\Profiler\Profile as HttpProfile;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[CoversNothing]
abstract class AbstractTelegramTestCase extends AbstractWebTestCase
{
    protected HttpClientInterface $telegramClient;
    protected SerializerInterface $serializer;
    protected PropertyAccessorInterface $pa;
    protected ChatterInterface $chatter;
    protected NotifierInterface $notifier;
    protected KernelBrowser $client;

    private string $appUrl;
    private string $testTelegramChatId;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        $container = self::getContainer();
        $this->telegramClient = $container->get(HttpClientInterface::class . ' $telegramClient');
        $this->serializer = $container->get(SerializerInterface::class);
        $this->pa = $container->get(PropertyAccessorInterface::class);
        $this->chatter = $container->get(ChatterInterface::class);
        $this->notifier = $container->get(NotifierInterface::class);
        $this->testTelegramChatId = $container->getParameter('app.test_telegram.chat_id');
        $this->appUrl = $container->getParameter('app.url');

        self::ensureKernelShutdown();
        $this->client = static::createClient();
    }

    /**
     * API
     */
    protected function postRequestTelegramWebhook(array $requestPayload): HttpProfile|null
    {
        $this->client->enableProfiler();
        $this->client->request('POST', $this->getTelegramWebhook(), $requestPayload);
        $profile = $this->client->getProfile();
        if (!$profile instanceof HttpProfile) {
            return null;
        }
        return $profile;
    }

    protected function getTestTelegramChatId(): string
    {
        return $this->testTelegramChatId;
    }

    protected function getAppUrl(): string
    {
        return $this->appUrl;
    }

    protected function getTelegramWebhook(): string
    {
        return $this->getAppUrl() . '/telegram/webhook';
    }

    protected function getMessageUpdatePayload(): array
    {
        return [
            "update_id" => 0,
            "message" => [
                "message_id" => 0,
                "from" => [
                    "id" => $this->getTestTelegramChatId(),
                    "is_bot" => false,
                    "first_name" => "Test first name",
                    "username" => "Test username",
                    "language_code" => "en",
                ],
                "chat" => [
                    "id" => $this->getTestTelegramChatId(),
                    "first_name" => 'Test first name',
                    "username" => "Test username",
                    "type" => "private",
                ],
                "date" => 0,
                "text" => "TEST",
            ],
        ];
    }

    protected function decodeResponse(ResponseInterface $response): array
    {
        return $this->serializer->decode($response->getContent(), 'json');
    }
}
