<?php

namespace App\Controller;

use App\Exception\Telegram\NotEnoughRequestPayloadTelegramException;
use App\Form\TelegramWebAppMainType;
use GrinWay\Telegram\Service\Telegram;
use GrinWay\Telegram\Type\TelegramLabeledPrice;
use GrinWay\Telegram\Type\TelegramLabeledPrices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\Attribute\Route;

// https://core.telegram.org/bots/api
#[Route('/telegram', 'app_')]
class TelegramController extends AbstractController
{
    public function __construct(
        private readonly ?ChatterInterface                                           $chatter,
        private readonly PropertyAccessorInterface                                   $pa,
        private readonly RequestStack                                                $requestStack,
        //private readonly Telegram                                                    $telegram,
        #[Autowire('%env(APP_TELEGRAM_Y_KASSA_API_TOKEN)%')] private readonly string $telegramYKassaToken,
    )
    {
    }

    /**
     * https://core.telegram.org/bots/api#createinvoicelink
     */
    #[Route('/invoice-link', 'invoice_link', options: ['expose' => true], methods: ['POST'])]
    public function getInvoiceLink(
        Request $request,
    ): Response
    {
        $payload = $request->getPayload();

        //required
        $title = $this->pa->getValue($payload->all(), '[title]');
        $description = $this->pa->getValue($payload->all(), '[description]');
        $prices = $payload->all('prices');
        $currency = $this->pa->getValue($payload->all(), '[currency]');

        if (null === $title
            || null === $description
            || empty($prices)
            || null === $currency
        ) {
            throw new NotEnoughRequestPayloadTelegramException([
                'title',
                'description',
                'prices',
                'currency',
            ]);
        }

        //optional
        $photoUri = $this->pa->getValue($payload->all(), '[photo_uri]');
        $needName = $this->pa->getValue($payload->all(), '[need_name]');
        $needPhoneNumber = $this->pa->getValue($payload->all(), '[need_phone_number]');
        $needEmail = $this->pa->getValue($payload->all(), '[need_email]');
        $needShippingAddress = $this->pa->getValue($payload->all(), '[need_shipping_address]');
        $sendPhoneNumberToProvider = $this->pa->getValue($payload->all(), '[send_phone_number_to_provider]');
        $sendEmailToProvider = $this->pa->getValue($payload->all(), '[send_email_to_provider]');
        $isFlexible = $this->pa->getValue($payload->all(), '[is_flexible]');
        $invoicePayload = $this->pa->getValue($payload->all(), '[payload]');
        $startParameter = $this->pa->getValue($payload->all(), '[start_parameter]');
        $providerData = $this->pa->getValue($payload->all(), '[provider_data]');
        $prependJsonRequest = $payload->all('prepend_json_request');
        $labelDopPriceToAchieveMinOneBecauseOfTelegramBotApi = $this->pa->getValue($payload->all(), '[label_dop_price_to_achieve_min_one_because_of_telegram_bot_api]');

        $invoiceLink = $this->telegram->createInvoiceLink(
            title: $title,
            description: $description,
            prices: $prices,
            providerToken: $this->telegramYKassaToken,
            currency: $currency,
            photoUri: $photoUri,
            needName: $needName,
            needPhoneNumber: $needPhoneNumber,
            needEmail: $needEmail,
            needShippingAddress: $needShippingAddress,
            sendPhoneNumberToProvider: $sendPhoneNumberToProvider,
            sendEmailToProvider: $sendEmailToProvider,
            isFlexible: $isFlexible,
            payload: $invoicePayload,
            startParameter: $startParameter,
            providerData: $providerData,
            prependJsonRequest: $prependJsonRequest,
            labelDopPriceToAchieveMinOneBecauseOfTelegramBotApi: $labelDopPriceToAchieveMinOneBecauseOfTelegramBotApi,
        );

        return new Response($invoiceLink, 200);
    }

    #[Route('/web-app', 'web_app', methods: ['GET', 'POST'])]
    public function webApp(
        Request $request,
    ): Response
    {
        \dump(
            $request->query->all(),
            $request->getPayload()->all(),
        );

        $form = $this->createForm(TelegramWebAppMainType::class);
        $form->handleRequest($request);

//        $submittedToken = $request->getPayload()->all()['_token'] ?? null;
//        if ($request->isMethod('POST') && $this->isCsrfTokenValid('submit', $submittedToken)) {
        if ($form->isSubmitted() && $form->isValid()) {
            $payload = $request->getPayload()->all();
            $title = $payload['button'] ?? 'NO DATA';
            $userId = $payload['user_id'] ?? null;

            if (null !== $userId) {
                $this->telegram->sendInvoice(
                    chatId: $userId,
                    title: $title,
                    description: 'description',
                    prices: new TelegramLabeledPrices(
                        new TelegramLabeledPrice('l1', '1000'),
                    ),
                    providerToken: $this->telegramYKassaToken,
                    currency: 'RUB',
                );
            }
            return $this->redirectToRoute($request->attributes->get('_route'), $request->attributes->get('_route_params'));
        }

        return $this->render('telegram/web-app.html.twig', [
            'form' => $form,
            'items' => [
                'Item 1',
                'Item 2',
                'Item 3',
            ],
        ]);
    }
}
