<?php

namespace App\Controller;

use App\Entity\Foundry;
use App\Router\RouteContent\DefaultRouteContent;
use App\Router\RouteObject\DefaultRoute;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DynamicController extends AbstractController
{
    public function __invoke(
        Foundry $contentDocument,
    ): Response
    {
        return $this->render('dynamic/index.html.twig', [
            'content' => $contentDocument,
        ]);
    }
}
