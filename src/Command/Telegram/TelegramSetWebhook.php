<?php

namespace App\Command\Telegram;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Service\Attribute\SubscribedService;

// TODO: TelegramSetWebhook
#[AsCommand(
    name: 'telegram:set_webhook',
    hidden: true,
)]
class TelegramSetWebhook extends Command
{
    public const HELP = 'Sets Telegram webhook';
    public const DESCRIPTION = self::HELP;

    private HttpClientInterface $client;
    private string $telegramApiToken;
    private string $appHost;
    private PropertyAccessorInterface $pa;
    private SerializerInterface $serializer;
    private ContainerInterface $serviceLocator;

    #[Required]
    public function _setRequired(
        HttpClientInterface       $client,
        #[Autowire('%env(APP_TELEGRAM_TOKEN)%')]
        string                    $telegramApiToken,
        #[Autowire('%env(APP_HOST)%')]
        string                    $appHost,
        PropertyAccessorInterface $pa,
        SerializerInterface       $serializer,
        #[AutowireLocator([
            'logger' => new SubscribedService(type: LoggerInterface::class, attributes: new Target('telegramLogger'))
        ])]                       $serviceLocator,
    ): void
    {
        $this->client = $client;
        $this->telegramApiToken = $telegramApiToken;
        $this->appHost = $appHost;
        $this->pa = $pa;
        $this->serializer = $serializer;
        $this->serviceLocator = $serviceLocator;
    }

    protected function execute(
        InputInterface  $input,
        OutputInterface $output,
    ): int
    {

        $webhookUri = \sprintf('https://%s/telegram/webhook', $this->appHost);
        $telegramBotApiUriSetWebhook = \sprintf(
            'https://api.telegram.org/bot%s/setWebhook?url=%s',
            $this->telegramApiToken,
            $webhookUri,
        );

        $response = $this->client->request('GET', $telegramBotApiUriSetWebhook, [
            'timeout' => 30,
        ]);

        $content = $response->getContent();
        $content = $this->serializer->decode($content, 'json');

        $ok = $this->pa->getValue($content, '[ok]');

        if (true === $ok) {
//            $result = $this->pa->getValue($content, '[result]');
            $description = $this->pa->getValue($content, '[description]');

            $message = \sprintf(
                'Telegram webhook: "%s"|Status: [%s]|Description: [%s]',
                $webhookUri,
                $ok,
                $description,
            );
        } else {
            $errorCode = $this->pa->getValue($content, '[error_code]');
            $parameters = $this->pa->getValue($content, '[parameters]');

            if ($this->serviceLocator->has($key = 'logger')) {
                $this->serviceLocator->get($key)->error('Error', ['error_code' => $errorCode, 'parameters' => $parameters]);
            }

            $message = \sprintf(
                'Telegram webhook: "%s"|Status: [%s]|See error info in the telegram logs',
                $webhookUri,
                $ok,
            );
        }

        $output->writeln(
            \explode('|', $message),
        );

        return Command::SUCCESS;
    }
}
