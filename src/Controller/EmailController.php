<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/email', name: 'app_email_')]
class EmailController extends AbstractController
{
    public function __construct(
        private readonly NotifierInterface                                   $notifier,
        private readonly string                                              $appTestEmail,
        #[Autowire('%env(APP_MAILER_HEADER_FROM)%')] private readonly string $emailHeaderFrom,
    )
    {
    }

    #[Route('/test', name: 'test', methods: ['GET'])]
    public function test(): Response
    {
        $notification = (new Notification('[TEST] Проврочка mailer/notifier', ['email']))
            ->content('Checking the mailer');
        $recipient = new Recipient(
            $this->appTestEmail,
        );

        $this->notifier->send($notification, $recipient);

        return $this->render('email/index.html.twig', [
            'to_email' => $this->appTestEmail,
            'header_from' => $this->emailHeaderFrom,
        ]);
    }
}
