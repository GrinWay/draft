<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApiController extends AbstractController
{
    #[Route('/track-me', name: 'app_track_me', options: ['expose' => true])]
    public function trackMe(): Response
    {
        return $this->render('api/track-me.html.twig', [
        ]);
    }

    #[Route('/api/login_check', name: 'app_login_check', methods: ['POST'])]
    public function login(): Response
    {
        return new Response('Logged in', 200);
    }
}
