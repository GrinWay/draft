<?php

namespace App\Controller;

use GrinWay\Telegram\Service\Telegram;
use Knp\Component\Pager\PaginatorInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use parallel\Channel;
use parallel\Runtime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Turbo\TurboBundle;

class HomeController extends AbstractController
{
    public function __construct(
        private readonly UrlGeneratorInterface                                       $urlGenerator,
        private readonly HttpClientInterface                                         $githubApiClient,
        private readonly string                                                      $publicImgDir,
        private readonly Packages                                                    $asset,
        #[Autowire('%env(resolve:APP_URL)%')] private readonly string                $appUrl,
        #[Autowire('%env(APP_TELEGRAM_Y_KASSA_API_TOKEN)%')] private readonly string $telegramYKassaApiToken,
        private readonly JWTTokenManagerInterface                                    $jwtManager,
        #[Autowire('%env(APP_TELEGRAM_BOT_API_TOKEN)%')] private readonly string     $telegramBotApiToken,
        #[Autowire('%env(resolve:APP_HOST)%')] private readonly string               $host,
        private readonly TranslatorInterface                                         $t,
    )
    {
    }

    #[Route('/event-source', 'app_event_source')]
    public function eventSource()
    {
        return new StreamedResponse(static function (): void {
            \ob_end_clean();
            $idx = 0;

            while (true) {
                echo \sprintf('event: mess%1$sdata: random data%2$s%1$sid: %3$s%1$s%1$s', \PHP_EOL, \random_int(0, 100), ++$idx);
                \flush();
                \sleep(1);
            }
        }, headers: [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
        ]);
    }

    #[Route('/custom-pagination', name: 'app_custom_paginator', options: ['expose' => true])]
    public function customPagination(
        PaginatorInterface $paginator,
                           $projectDir,
        Request            $request,
    )
    {
        $pagination = $paginator->paginate($projectDir, $request->query->getInt('page', 1), 10);

        $template = 'home/custom_pagination.html.twig';
        $parameters = [
            'pagination' => $pagination,
        ];
        return $this->render($template, $parameters);
    }

    /**
     *
     * ###> HOME ###
     *
     * @param Request $request
     * @param $projectDir
     * @return Response
     */
    #[Route('/', name: 'app_home', options: ['expose' => true])]
    public function home(
        Request                                              $request,
        string                                               $projectDir,
        HttpClientInterface                                  $grinwayTelegramClient,
        HttpClientInterface                                  $grinwayServiceCurrencyFixerLatestUsd,
        #[Autowire('@grinway_service.currency')]             $currencyService,
        SerializerInterface                                  $serializer,
        ?ChatterInterface                                    $chatter,
        #[Autowire('%env(APP_TELEGRAM_TOKEN)%')] string      $appTelegramToken,
        Telegram                                             $telegram,
        PropertyAccessorInterface                            $pa,
        #[Autowire('%kernel.trusted_proxies%')]              $kernelParam,
        #[Autowire('%env(APP_TELEGRAM_Y_KASSA_API_TOKEN)%')] $providerToken,
    ): Response
    {
		\dump($telegram->createInvoiceLink(
			title: '$title',
			description: '$description',
			prices: [
				['label' => 'l', 'amount' => '100000',], // one dollar
				//['label' => 'l', 'amount' => '111',], // one dollar
				//['label' => 'l', 'amount' => '1 end_dollar / 2',], // one dollar
				//['label' => 'l', 'amount' => '1 end_dollar',], // one dollar
				//['label' => 'l', 'amount' => '1end_dollar_more',], // one dollar

				//['label' => 'l', 'amount' => 'one_dollar / 2',], // one dollar
				
				//['label' => 'l', 'amount' => 'start_dollar end_dollar',], // one dollar
				
				//['label' => 'l', 'amount' => 'start_dollar end_dollar_more',], // start_dollar passed_end
				//['label' => 'l', 'amount' => 'start_dollar end_dollar_less',], // start_dollar+1 end_passed
				//['label' => 'l', 'amount' => 'start_dollar end_dollar / 2',], // start_dollar+1 end_passed
				//['label' => 'l', 'amount' => 'start_dollar end_dollar_more',], // start_dollar+1 end_passed
				//['label' => 'l', 'amount' => 'start_dollar-1 end_dollar_more',], // start_dollar end passed
				//['label' => 'l', 'amount' => '9799',], // start_dollar end passed
			
				//['label' => 'l', 'amount' => 'start_dollar+1 end_dollar_less',], // passed
				//['label' => 'l', 'amount' => 'start_dollar+1 end_dollar / 2',], // passed
				//['label' => 'l', 'amount' => 'start_dollar+1 end_dollar_more',], // passed
			],
			providerToken: $providerToken,
			currency: 'RUB',
		));
		
		//$response = $grinwayServiceCurrencyFixerLatestUsd->request('GET', '');
		//\dump(
			//$serializer->decode($response->getContent(), 'json'),
		//);
		
		//\dump($currencyService->transferAmountFromTo('100', 'USD', 'RUB'));
		
		
        $template = 'home/index.html.twig';
        $parameters = [
        ];
        $response = $this->render($template, $parameters);
        return $response;
    }

    /**
     * @param Request $request
     * @param HubInterface $hub
     * @return Response
     */
    #[Route('/turbo/stream/test', name: 'app_mercure_test', options: ['expose' => true])]
    public function mercureTest(
        Request      $request,
        HubInterface $hub,
    ): Response
    {
        $preferredFormat = $request->getPreferredFormat();
        \dump($preferredFormat);

        if (TurboBundle::STREAM_FORMAT === $preferredFormat) {
            $turboStreamView = $this->renderView($template = 'home/test.stream.html.twig');
            $hub->publish(new Update(
                ['test'],
                $turboStreamView,
            ));
        }

        return $this->redirectToRoute('app_home', [
        ]);
    }

    public function noRouteMethod()
    {
        return $this->render('home/test_escaping_strategy_as_first_ext.js.twig');
    }
}
