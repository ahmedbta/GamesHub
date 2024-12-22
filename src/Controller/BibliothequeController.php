<?php

namespace App\Controller;

use App\Entity\Bibliotheque;
use App\Entity\Jeu;
use App\Entity\Member;
use App\Form\BibliothequeType;
use App\Repository\BibliothequeRepository;
use App\Repository\JeuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

#[Route('/bibliotheque')]
final class BibliothequeController extends AbstractController
{
    #[Route(name: 'app_bibliotheque_index', methods: ['GET'])]
public function index(BibliothequeRepository $bibliothequeRepository): Response
{
    $user = $this->getUser();
    
    if (!$user) {
        $this->addFlash('error', 'Vous devez être connecté pour voir vos bibliothèques.');
        return $this->redirectToRoute('app_login');
    }

    $bibliotheques = $this->isGranted('ROLE_ADMIN')
        ? $bibliothequeRepository->findAll()
        : $bibliothequeRepository->findAccessibleBibliotheques($user);

    return $this->render('bibliotheque/index.html.twig', [
        'bibliotheques' => $bibliotheques,
    ]);
}

    

    #[Route('/{id}', name: 'app_bibliotheque_show', methods: ['GET'])]
public function show(Bibliotheque $bibliotheque): Response
{
    $hasAccess = false;

    if ($this->isGranted('ROLE_ADMIN') || $bibliotheque->isPubliee()) {
        $hasAccess = true;
    } else {
        $member = $this->getUser();
        if ($member && $member === $bibliotheque->getCreateur()) {
            $hasAccess = true;
        }
    }

    if (!$hasAccess) {
        throw $this->createAccessDeniedException("Vous ne pouvez pas accéder à cette ressource.");
    }

    return $this->render('bibliotheque/show.html.twig', [
        'bibliotheque' => $bibliotheque,
    ]);
}


    #[Route('/{id}/edit', name: 'app_bibliotheque_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Bibliotheque $bibliotheque, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() !== $bibliotheque->getCreateur() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas modifier cette bibliothèque.");
        }

        $form = $this->createForm(BibliothequeType::class, $bibliotheque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_member_show', ['id' => $bibliotheque->getCreateur()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('bibliotheque/edit.html.twig', [
            'bibliotheque' => $bibliotheque,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_bibliotheque_delete', methods: ['POST'])]
    public function delete(Request $request, Bibliotheque $bibliotheque, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() !== $bibliotheque->getCreateur() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas supprimer cette bibliothèque.");
        }

        if ($this->isCsrfTokenValid('delete' . $bibliotheque->getId(), $request->request->get('_token'))) {
            $entityManager->remove($bibliotheque);
            $entityManager->flush();

            return $this->redirectToRoute('app_member_show', ['id' => $bibliotheque->getCreateur()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('app_bibliotheque_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{bibliotheque_id}/jeu/{jeu_id}', name: 'app_bibliotheque_jeu_show', methods: ['GET'])]
public function jeuShow(
    #[MapEntity(id: 'bibliotheque_id')] Bibliotheque $bibliotheque,
    #[MapEntity(id: 'jeu_id')] Jeu $jeu
): Response {
    if (!$bibliotheque->getJeux()->contains($jeu)) {
        throw $this->createNotFoundException("Ce jeu n'appartient pas à cette bibliothèque.");
    }

    $hasAccess = false;

    if ($this->isGranted('ROLE_ADMIN') || $bibliotheque->isPubliee()) {
        $hasAccess = true;
    } else {
        $member = $this->getUser();
        if ($member && $member === $bibliotheque->getCreateur()) {
            $hasAccess = true;
        }
    }

    if (!$hasAccess) {
        throw $this->createAccessDeniedException("Vous ne pouvez pas accéder à ce jeu.");
    }

    return $this->render('bibliotheque/jeu_show.html.twig', [
        'jeu' => $jeu,
        'bibliotheque' => $bibliotheque,
    ]);
}

#[Route('/new/{memberId}', name: 'app_bibliotheque_new', methods: ['GET', 'POST'])]
public function new(int $memberId, Request $request, EntityManagerInterface $entityManager, ManagerRegistry $doctrine,JeuRepository $jeuRepository): Response
{
    $member = $doctrine->getRepository(Member::class)->find($memberId);

    if (!$member) {
        throw $this->createNotFoundException('Membre non trouvé.');
    }

    if ($this->getUser() !== $member && !$this->isGranted('ROLE_ADMIN')) {
        throw $this->createAccessDeniedException("Vous ne pouvez pas créer une bibliothèque pour un autre membre.");
    }

    $bibliotheque = new Bibliotheque();
    $bibliotheque->setCreateur($member);
    $availableJeux = $jeuRepository->findAll();


    $form = $this->createForm(BibliothequeType::class, $bibliotheque);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($bibliotheque);
        $entityManager->flush();

        return $this->redirectToRoute('app_member_show', ['id' => $member->getId()]);
    }

    return $this->render('bibliotheque/new.html.twig', [
        'bibliotheque' => $bibliotheque,
        'form' => $form,
        'available_jeux' => $availableJeux, 

    ]);
}


}
