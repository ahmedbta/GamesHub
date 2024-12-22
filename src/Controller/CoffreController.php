<?php

namespace App\Controller;

use App\Entity\Coffre;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\CoffreType;


class CoffreController extends AbstractController
{
    #[Route('/coffres', name: 'coffre_index')]
    public function index(ManagerRegistry $doctrine): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('warning', 'Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('app_login');
        }
    
        $coffres = $doctrine->getRepository(Coffre::class)->findAll();
    
        return $this->render('coffre/index.html.twig', [
            'coffres' => $coffres,
        ]);
    }
    
    #[Route('/coffre/{id}', name: 'coffre_show')]
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $coffre = $doctrine->getRepository(Coffre::class)->find($id);

        if (!$coffre) {
            throw $this->createNotFoundException('Coffre non trouvé.');
        }

        if ($this->getUser() !== $coffre->getMembre() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à consulter ce coffre.");
        }

        return $this->render('coffre/show.html.twig', [
            'coffre' => $coffre,
        ]);
    }


}
