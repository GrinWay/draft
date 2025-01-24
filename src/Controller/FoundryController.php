<?php

namespace App\Controller;

use App\Entity\Foundry;
use App\Form\FoundryType;
use App\Repository\FoundryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/foundry')]
final class FoundryController extends AbstractController
{
    #[Route(name: 'app_foundry_index', methods: ['GET'])]
    public function index(FoundryRepository $foundryRepository): Response
    {
//        throw new \Exception('MESSAGE');

        return $this->render('foundry/index.html.twig', [
            'foundries' => $foundryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_foundry_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $foundry = new Foundry();
        $form = $this->createForm(FoundryType::class, $foundry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($foundry);
            $entityManager->flush();

            return $this->redirectToRoute('app_foundry_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('foundry/new.html.twig', [
            'foundry' => $foundry,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_foundry_show', methods: ['GET'])]
    public function show(Foundry $foundry): Response
    {
        return $this->render('foundry/show.html.twig', [
            'foundry' => $foundry,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_foundry_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Foundry $foundry, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FoundryType::class, $foundry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_foundry_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('foundry/edit.html.twig', [
            'foundry' => $foundry,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_foundry_delete', methods: ['POST'])]
    public function delete(Request $request, Foundry $foundry, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$foundry->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($foundry);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_foundry_index', [], Response::HTTP_SEE_OTHER);
    }
}
