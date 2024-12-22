<?php

namespace App\Controller;

use App\Entity\Jeu;
use App\Entity\Coffre;
use App\Form\JeuType;
use App\Entity\Bibliotheque;
use App\Repository\JeuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

#[Route('/jeu')]
final class JeuController extends AbstractController
{
    #[Route(name: 'app_jeu_index', methods: ['GET'])]
    public function index(Request $request, JeuRepository $jeuRepository, Security $security): Response
    {
        $user = $security->getUser();
        $selectedType = $request->query->get('type', '');
    
        if ($this->isGranted('ROLE_ADMIN')) {
            $jeus = $selectedType 
                ? $jeuRepository->findBy(['type' => $selectedType]) 
                : $jeuRepository->findAll();
        } else {
            $queryBuilder = $jeuRepository->createQueryBuilder('j')
                ->join('j.coffre', 'c')
                ->where('c.membre = :user')
                ->setParameter('user', $user);
    
            if ($selectedType) {
                $queryBuilder->andWhere('j.type = :type')
                    ->setParameter('type', $selectedType);
            }
    
            $jeus = $queryBuilder->getQuery()->getResult();
        }
    
        $gameTypes = $jeuRepository->createQueryBuilder('j')
            ->select('j.type')
            ->distinct()
            ->getQuery()
            ->getResult();
    
        return $this->render('jeu/index.html.twig', [
            'jeus' => $jeus,
            'gameTypes' => array_column($gameTypes, 'type'),
            'selectedType' => $selectedType,
        ]);
    }
    




    #[Route('/new/{id}', name: 'app_jeu_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Coffre $coffre): Response
    {
        if ($this->getUser() !== $coffre->getMembre() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à ajouter un jeu à ce coffre.");
        }

        $jeu = new Jeu();
        $jeu->setCoffre($coffre);

        $form = $this->createForm(JeuType::class, $jeu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($jeu);
            $entityManager->flush();

            return $this->redirectToRoute('coffre_show', ['id' => $coffre->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('jeu/new.html.twig', [
            'jeu' => $jeu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_jeu_show', methods: ['GET'])]
    public function show(Jeu $jeu): Response
    {
        if ($this->getUser() !== $jeu->getCoffre()->getMembre() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à consulter ce jeu.");
        }

        return $this->render('jeu/show.html.twig', [
            'jeu' => $jeu,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_jeu_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Jeu $jeu, ManagerRegistry $doctrine, Security $security): Response
{
    $user = $security->getUser();

    $userBibliotheques = $doctrine->getRepository(Bibliotheque::class)->findBy(['createur' => $user]);

    $form = $this->createForm(JeuType::class, $jeu, [
        'bibliotheques' => $userBibliotheques 
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $doctrine->getManager()->flush();

        return $this->redirectToRoute('app_jeu_index');
    }

    return $this->render('jeu/edit.html.twig', [
        'jeu' => $jeu,
        'form' => $form->createView(),
    ]);
}


    #[Route('/{id}', name: 'app_jeu_delete', methods: ['POST'])]
    public function delete(Request $request, Jeu $jeu, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() !== $jeu->getCoffre()->getMembre() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à supprimer ce jeu.");
        }

        if ($this->isCsrfTokenValid('delete'.$jeu->getId(), $request->request->get('_token'))) {
            $entityManager->remove($jeu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_jeu_index', [], Response::HTTP_SEE_OTHER);
    }
}