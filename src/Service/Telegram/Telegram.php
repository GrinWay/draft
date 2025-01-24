<?php

namespace App\Service\Telegram;

use App\Service\CurrencyService;
use App\Service\FiguresRepresentation;
use App\Validator\AbsolutePath;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Intl\Transliterator\EmojiTransliterator;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

// TODO: Telegram
class Telegram
{
    public const LENGTH_AMOUNT_END_FIGURES = 2;
    public const MIN_START_AMOUNT_PART = 1;
    public const TELEGRAM_STARS_CURRENCY = 'XTR';
    /**
     * Filled by DynamicTagPass in the Kernel::build()
     *
     * @var array Array of Service locators with handler_tag keys
     */
    private array $updateHandlerIterators = [];

    public function __construct(
        private readonly HttpClientInterface       $telegramClient,
        private readonly HttpClientInterface       $telegramFileClient,
        private readonly SerializerInterface       $serializer,
        private readonly PropertyAccessorInterface $pa,
        private readonly Filesystem                $filesystem,
        private readonly SluggerInterface          $slugger,
        private readonly CurrencyService           $currencyService,
        private readonly TranslatorInterface       $t,
    )
    {
    }

    /**
     * Automatically makes a directory if it doesn't exist
     *
     * @return bool True if made, false if not
     */
    public function downloadFile(string $fileId, string $absFilepathTo, bool $overwrite = false, bool $throw = false): bool
    {
        if (!Validation::createIsValidCallable(new AbsolutePath())($absFilepathTo)) {
            if (true === $throw) {
                throw new \LogicException(\sprintf('You passed not an absolute path: "%s"', $absFilepathTo));
            }
            return false;
        }

        if (\is_file($absFilepathTo) && false === $overwrite) {
            return false;
        }

        try {
            $content = $this->request('POST', 'getFile', [
                'file_id' => $fileId,
            ]);
        } catch (\Exception $exception) {
            if (true === $throw) {
                throw $exception;
            }
            return false;
        }

        $ok = $this->pa->getValue($content, '[ok]');
        if (true !== $ok) {
            return false;
        }

        $filepath = $this->pa->getValue($content, '[result][file_path]');
        if (empty($filepath)) {
            return false;
        }

        try {
            $response = $this->telegramFileClient->request('GET', \ltrim($filepath, '/\\'));
        } catch (\Exception $exception) {
            if (true === $throw) {
                throw $exception;
            }
            return false;
        }

        $absPathTo = Path::getDirectory($absFilepathTo);
        if (!\is_dir($absPathTo)) {
            $this->filesystem->mkdir($absPathTo);
        }
        $handler = \fopen($absFilepathTo, 'wb');
        foreach ($this->telegramFileClient->stream($response) as $chunk) {
            \fwrite($handler, $chunk->getContent());
        }
        \fclose($handler);
        return true;
    }

    public function downloadStickers(string $stickersName, string $absDirTo, bool $overwrite = false, string $prefixFilename = '', bool $throw = false, ?int $limit = null): array
    {
        $made = [];
        $transliterator = EmojiTransliterator::create('emoji-text');

        try {
            $payload = $this->request('POST', 'getStickerSet', [
                'name' => $stickersName,
            ]);
        } catch (\Exception $exception) {
            if (true === $throw) {
                throw $exception;
            }
            return $made;
        }

        $stickerSetName = \sprintf('%s%s', $prefixFilename, $this->pa->getValue($payload, '[result][name]'));

        $fileIdsObject = $this->pa->getValue($payload, '[result][stickers]');
        if (\is_array($fileIdsObject)) {
            $i = 0;
            $limitCounter = 0;
            foreach ($fileIdsObject as $fileIdObject) {
                if (null !== $limit && ++$limitCounter > $limit) {
                    break;
                }
                $fileId = $this->pa->getValue($fileIdObject, '[file_id]');
                if ($fileId) {
                    if (empty($prefixFilename)) {
                        $prefix = '%s';
                    } else {
                        $prefix = '%s_';
                    }
                    $emoji = $this->pa->getValue($fileIdObject, '[emoji]') ?: $i++;
                    $emojiTextRepresentation = $transliterator->transliterate($emoji);
                    $filename = (string)$this->slugger->slug(
                        \sprintf($prefix . '%s', $stickerSetName, $emojiTextRepresentation),
                    );
                    $absFilepathTo = \sprintf('%s/%s.webp', $absDirTo, $filename);
                    $wasMade = $this->downloadFile(
                        $fileId,
                        $absFilepathTo,
                        overwrite: $overwrite,
                        throw: $throw,
                    );
                    if (true === $wasMade) {
                        $made[$absFilepathTo] = $absFilepathTo;
                    }
                }
            }
        }
        return $made;
    }

    public function deleteMessage(?string $chatId, ?string $messageId, bool $throw = false): bool
    {
        if (null === $chatId || null === $messageId) {
            return false;
        }

        try {
            $responsePayload = $this->request('POST', 'deleteMessage', [
                'chat_id' => $chatId,
                'message_id' => $messageId,
            ]);
        } catch (\Exception $exception) {
            if (true === $throw) {
                throw $exception;
            }
            return false;
        }
        return $this->isResponsePayloadOk($responsePayload);
    }

    /**
     * @param string|int $key
     * @return iterable ServiceLocator
     */
    public function getUpdateHandlerIterator(string|int $key): iterable
    {
        return $this->updateHandlerIterators[$key] ?? [];
    }

    public function setUpdateHandlerIterator(string|int $key, iterable $iterable): static
    {
        $this->updateHandlerIterators[$key] = $iterable;
        return $this;
    }

    public function sendInvoice(
        string                      $chatId,
        string                      $title,
        string                      $description,
        TelegramLabeledPrices|array $prices,
        ?string                     $providerToken = null,
        ?string                     $currency = null,
        ?string                     $photoUri = null,
        ?bool                       $needName = null,
        ?bool                       $needPhoneNumber = null,
        ?bool                       $needEmail = null,
        ?bool                       $needShippingAddress = null,
        ?bool                       $sendPhoneNumberToProvider = null,
        ?bool                       $sendEmailToProvider = null,
        ?bool                       $isFlexible = null,
        ?string                     $payload = null,
        ?string                     $startParameter = null,
        ?array                      $providerData = null,
        ?array                      $prependJsonRequest = null,
        ?string                     $labelDopPriceToAchieveMinOneBecauseOfTelegramBotApi = null,
        ?bool                       $throw = null,
    ): bool
    {
        try {
            $invoicePayload = $this->getInvoicePayload(
                title: $title,
                description: $description,
                prices: $prices,
                chatId: $chatId,
                providerToken: $providerToken,
                currency: $currency,
                photoUri: $photoUri,
                needName: $needName,
                needPhoneNumber: $needPhoneNumber,
                needEmail: $needEmail,
                needShippingAddress: $needShippingAddress,
                sendPhoneNumberToProvider: $sendPhoneNumberToProvider,
                sendEmailToProvider: $sendEmailToProvider,
                isFlexible: $isFlexible,
                payload: $payload,
                startParameter: $startParameter,
                providerData: $providerData,
                prependJsonRequest: $prependJsonRequest,
                labelDopPriceToAchieveMinOneBecauseOfTelegramBotApi: $labelDopPriceToAchieveMinOneBecauseOfTelegramBotApi,
                throw: $throw,
            );
            $responsePayload = $this->request('POST', 'sendInvoice', $invoicePayload);
        } catch (\Exception $exception) {
            if (true === $throw) {
                throw $exception;
            }
            return false;
        }
        return $this->isResponsePayloadOk($responsePayload);
    }

    public function createInvoiceLink(
        string                      $title,
        string                      $description,
        TelegramLabeledPrices|array $prices,
        ?string                     $providerToken = null,
        ?string                     $currency = null,
        ?string                     $photoUri = null,
        ?bool                       $needName = null,
        ?bool                       $needPhoneNumber = null,
        ?bool                       $needEmail = null,
        ?bool                       $needShippingAddress = null,
        ?bool                       $sendPhoneNumberToProvider = null,
        ?bool                       $sendEmailToProvider = null,
        ?bool                       $isFlexible = null,
        ?string                     $payload = null,
        ?string                     $startParameter = null,
        ?array                      $providerData = null,
        ?array                      $prependJsonRequest = null,
        ?string                     $labelDopPriceToAchieveMinOneBecauseOfTelegramBotApi = null,
        ?bool                       $throw = null,
    ): ?string
    {
        try {
            $invoicePayload = $this->getInvoicePayload(
                title: $title,
                description: $description,
                prices: $prices,
                providerToken: $providerToken,
                currency: $currency,
                photoUri: $photoUri,
                needName: $needName,
                needPhoneNumber: $needPhoneNumber,
                needEmail: $needEmail,
                needShippingAddress: $needShippingAddress,
                sendPhoneNumberToProvider: $sendPhoneNumberToProvider,
                sendEmailToProvider: $sendEmailToProvider,
                isFlexible: $isFlexible,
                payload: $payload,
                startParameter: $startParameter,
                providerData: $providerData,
                prependJsonRequest: $prependJsonRequest,
                labelDopPriceToAchieveMinOneBecauseOfTelegramBotApi: $labelDopPriceToAchieveMinOneBecauseOfTelegramBotApi,
                throw: $throw,
            );
            $responsePayload = $this->request('POST', 'createInvoiceLink', $invoicePayload);
        } catch (\Exception $exception) {
            if (true === $throw) {
                throw $exception;
            }
            return null;
        }
        if ($this->isResponsePayloadOk($responsePayload)) {
            return $this->pa->getValue($responsePayload, '[result]');
        }
        return null;
    }

    /**
     * The lowest amount (price) is 1$ (it's a Telegram Bot Api restriction)
     * Using this method by default 1$ amount in a chosen currency will be set if described price will be lower than 1$
     * Otherwise an Error would be
     *
     * https://core.telegram.org/bots/api#sendinvoice
     *
     * https://yookassa.ru/docs/support/payments/onboarding/integration/cms-module/telegram#telegram__03
     * Yoo Kassa test cards: https://yookassa.ru/developers/payment-acceptance/testing-and-going-live/testing#test-bank-card-success
     *
     * @param ?string $startParameter Has a string by default in order to start private conversation with the bot by '/start <parameter>'
     * @param ?array $providerData If your data is already got just set it straight away
     * @param bool $isFlexible Is the final price depends on shipping method
     *     https://yookassa.ru/developers/api#create_payment
     *     https://yookassa.ru/developers/payment-acceptance/receipts/54fz/yoomoney/parameters-values#payment-subject
     * @throws \LogicException REGARDLESS $throw parameter: If combination of $currency and $providerToken is incorrect
     * @throws \LogicException REGARDLESS $throw parameter: When your currency is telegram stars you have to have exactly 1 $prices item
     * @throws \LogicException REGARDLESS $throw parameter: At least ONE or more elements of a certain type must exist in the $prices array
     */
    protected function getInvoicePayload(
        string                      $title,
        string                      $description,
        TelegramLabeledPrices|array $prices,
        ?string                     $chatId = null,
        ?string                     $providerToken = null,
        ?string                     $currency = null,
        ?string                     $photoUri = null,
        ?bool                       $needName = null,
        ?bool                       $needPhoneNumber = null,
        ?bool                       $needEmail = null,
        ?bool                       $needShippingAddress = null,
        ?bool                       $sendPhoneNumberToProvider = null,
        ?bool                       $sendEmailToProvider = null,
        ?bool                       $isFlexible = null,
        ?string                     $payload = null,
        ?string                     $startParameter = null,
        ?array                      $providerData = null,
        ?array                      $prependJsonRequest = null,
        ?string                     $labelDopPriceToAchieveMinOneBecauseOfTelegramBotApi = null,
        ?bool                       $throw = null,
    ): array
    {
        //defaults
        $needName ??= false;
        $needPhoneNumber ??= false;
        $needEmail ??= false;
        $needShippingAddress ??= false;
        $sendPhoneNumberToProvider ??= false;
        $sendEmailToProvider ??= false;
        $isFlexible ??= false;
        $prependJsonRequest ??= [];
        $throw ??= false;

        // At least these settings must exist by default
        $payload ??= '{}';
        $currency ??= self::TELEGRAM_STARS_CURRENCY;
        $providerToken ??= '';
        $startParameter ??= 'service';
        $labelDopPriceToAchieveMinOneBecauseOfTelegramBotApi ??= 'label_dop_price_to_achieve_min_one';

        // currency, provider token VALIDATION
        if (self::TELEGRAM_STARS_CURRENCY === $currency) {
            $needName = false;
            $needPhoneNumber = false;
            $needEmail = false;
            $needShippingAddress = false;
            $sendEmailToProvider = false;
            $sendPhoneNumberToProvider = false;
        }
        if (self::TELEGRAM_STARS_CURRENCY === $currency && '' !== $providerToken) {
            if (true === $throw) {
                throw new \LogicException(\sprintf('If currency is "%s", provider token MUST be \'\' (empty string) but you passed "%s"', $currency, $providerToken));
            } else {
                $providerToken = '';
            }
        }
        if (self::TELEGRAM_STARS_CURRENCY !== $currency && '' === $providerToken) {
            throw new \LogicException(\sprintf('Currency is not "%s" you MUST point out the provider token', self::TELEGRAM_STARS_CURRENCY));
        }
        if (self::TELEGRAM_STARS_CURRENCY === $currency && !Validation::createIsValidCallable(
                new Assert\Count(exactly: 1)
            )($prices)
        ) {
            throw new \LogicException(\sprintf(
                'When your currency is "%s" you have to have exactly 1 $prices item',
                self::TELEGRAM_STARS_CURRENCY,
            ));
        }

        // prices VALIDATION
        if (!Validation::createIsValidCallable(
            new Assert\Count(min: 1),
            new Assert\AtLeastOneOf([
                new Assert\Type(TelegramLabeledPrices::class),
                new Assert\Type('array'),
            ]))($prices)
        ) {
            throw new \InvalidArgumentException(\sprintf(
                    'At least ONE or more elements of type: "%s" must exist in the $prices',
                    TelegramLabeledPrice::class,
                )
            );
        }
        if (\is_array($prices)) {
            $prices = $this->transformPricesArrayToTelegramLabeledPrices($prices);
        }
        $this->appendDopPriceIfAmountLessThanPossibleLowestPrice(
            $prices,
            $labelDopPriceToAchieveMinOneBecauseOfTelegramBotApi,
            $currency,
        );
        if ($prices instanceof TelegramLabeledPrices) {
            $prices = $prices->toArray();
        }

        $invoicePayload = \array_merge($prependJsonRequest, [
            'title' => $title,
            'description' => $description,
            'payload' => $payload,
            'currency' => $currency,
            'prices' => $prices,
            'provider_token' => $providerToken,
            'need_name' => $needName,
            'need_phone_number' => $needPhoneNumber,
            'need_email' => $needEmail,
            'need_shipping_address' => $needShippingAddress,
            'send_phone_number_to_provider' => $sendPhoneNumberToProvider,
            'send_email_to_provider' => $sendEmailToProvider,
            'is_flexible' => $isFlexible,
            'start_parameter' => $startParameter,
        ]);

        if (null !== $chatId) {
            $invoicePayload['chat_id'] = $chatId;
        }
        if (null !== $photoUri) {
            $invoicePayload['photo_url'] = $photoUri;
        }
        if (null !== $providerData) {
            $invoicePayload['provider_data'] = $providerData;
        }

        return $invoicePayload;
    }

    /**
     * https://core.telegram.org/bots/api#answerinlinequery
     * @param string $type https://core.telegram.org/bots/api#inlinequeryresult
     * @param ?string $id https://core.telegram.org/bots/api#inlinequeryresult
     * @param array $results https://core.telegram.org/bots/api#inlinequeryresult
     */
    public function answerInlineQuery(string $inlineQueryId, string $type, array $results, ?string $id = null, bool $throw = false): bool
    {
        // https://core.telegram.org/bots/api#inlinequeryresultgif
        $id ??= (string)\substr(\uniqid('', true), 0, 64);

        try {
            $responsePayload = $this->request('POST', 'answerInlineQuery', [
                'inline_query_id' => $inlineQueryId,
                'results' => [
                    \array_merge([
                        'type' => $type,
                        'id' => $id,
                    ], $results),
                ],
            ]);
        } catch (\Exception $exception) {
            if (true === $throw) {
                throw $exception;
            }
            return false;
        }
        return $this->isResponsePayloadOk($responsePayload);
    }

    /**
     * https://core.telegram.org/bots/api#answershippingquery
     * @param array $shippingOptions Array of https://core.telegram.org/bots/api#shippingoption
     * @return bool
     */
    public function answerShippingQuery(string $shippingQueryId, array $shippingOptions, true|string $shippingQueryIsValid, bool $throw = false): bool
    {
        $okJson = [
            'shipping_query_id' => $shippingQueryId,
            'ok' => true,
            'shipping_options' => $shippingOptions,
        ];
        $errorJson = [
            'shipping_query_id' => $shippingQueryId,
            'ok' => false,
            'error_message' => $shippingQueryIsValid,
        ];

        if (true === $shippingQueryIsValid) {
            $requestJson = $okJson;
        } else {
            $requestJson = $errorJson;
        }

        try {
            $responsePayload = $this->request('POST', 'answerShippingQuery', $requestJson);
        } catch (\Exception $exception) {
            if (true === $throw) {
                throw $exception;
            }
            return false;
        }
        return $this->isResponsePayloadOk($responsePayload);
    }

    /**
     * https://core.telegram.org/bots/api#answerprecheckoutquery
     */
    public function answerPreCheckoutQuery(string $preCheckoutQueryId, true|string $preCheckoutQueryIsValid, bool $throw = false): bool
    {
        $okJson = [
            'pre_checkout_query_id' => $preCheckoutQueryId,
            'ok' => true,
        ];
        $errorJson = [
            'pre_checkout_query_id' => $preCheckoutQueryId,
            'ok' => false,
            'error_message' => $preCheckoutQueryIsValid,
        ];

        if (true === $preCheckoutQueryIsValid) {
            $requestJson = $okJson;
        } else {
            $requestJson = $errorJson;
        }

        try {
            $responsePayload = $this->request('POST', 'answerPreCheckoutQuery', $requestJson);
        } catch (\Exception $exception) {
            if (true === $throw) {
                throw $exception;
            }
            return false;
        }
        return $this->isResponsePayloadOk($responsePayload);
    }

    /**
     * @return mixed response payload
     */
    private function request(string $method, string $url, array $json): mixed
    {
        $response = $this->telegramClient->request($method, $url, [
            'json' => $json,
        ]);
        return $this->serializer->decode($response->getContent(), 'json');
    }

    private function isResponsePayloadOk(mixed $responsePayload): bool
    {
        if (!\is_array($responsePayload)) {
            return false;
        }
        return true === $this->pa->getValue($responsePayload, '[ok]');
    }

    private function appendDopPriceIfAmountLessThanPossibleLowestPrice(TelegramLabeledPrices $prices, string $labelDopPriceToAchieveMinOneBecauseOfTelegramBotApi, mixed $currency): void
    {
        $dopStartAmountNumber = 0;
        $dopEndAmountNumber = 0;
        [$startPassedAmount, $endPassedAmount] = $prices->getStartEndSumNumbers();
        $oneDollarToPassedCurrency = $this->currencyService->transferAmountFromTo(
            '100',
            'USD',
            $currency,
        );
        [$startOneDollarAmountInCurrentCurrency, $endOneDollarAmountInCurrentCurrency] = $prices->getStartEndNumbers(
            $oneDollarToPassedCurrency
        );
        //###> LESS than MIN
        if ($startPassedAmount <= $startOneDollarAmountInCurrentCurrency) {
            $dopStartAmountNumber = $startOneDollarAmountInCurrentCurrency - $startPassedAmount;
            // after start
            if ($endPassedAmount !== $endOneDollarAmountInCurrentCurrency) {
                if (self::MIN_START_AMOUNT_PART < $dopStartAmountNumber) {
                    --$dopStartAmountNumber;
                    $dopEndAmountNumber = (10 ** self::LENGTH_AMOUNT_END_FIGURES) - \abs($endPassedAmount - $endOneDollarAmountInCurrentCurrency);
                }
            }
        }
        if (0 !== $dopStartAmountNumber || 0 !== $dopEndAmountNumber) {
            $dopAmountWithEndFigures = FiguresRepresentation::concatNumbersWithCorrectCountOfEndFigures(
                $dopStartAmountNumber,
                $dopEndAmountNumber,
            );
            $prices[] = new TelegramLabeledPrice(
                $this->t->trans($labelDopPriceToAchieveMinOneBecauseOfTelegramBotApi, domain: 'grinway.telegram'),
                $dopAmountWithEndFigures,
            );
        }
    }

    private function transformPricesArrayToTelegramLabeledPrices(array $prices): TelegramLabeledPrices
    {
        $transformedPrices = [];
        foreach ($prices as $price) {
            $label = $price['label'];
            $amount = $price['amount'];
            $transformedPrices[] = new TelegramLabeledPrice($label, $amount);
        }
        return new TelegramLabeledPrices(...$transformedPrices);
    }
}
