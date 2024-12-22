<?php

namespace App\Controller;

use App\Entity\Member;
use App\Repository\MemberRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/member')]
class MemberController extends AbstractController
{
    #[Route('/', name: 'app_member_index', methods: ['GET'])]
    public function index(MemberRepository $memberRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, "Vous n'avez pas les permissions nécessaires pour accéder à cette page.");

        return $this->render('member/index.html.twig', [
            'members' => $memberRepository->findAll(),
        ]);
    }

    
    #[Route('/{id}', name: 'app_member_show', methods: ['GET'])]
public function show(?Member $member): Response
{
    if (!$member) {
        throw $this->createNotFoundException('Membre introuvable.');
    }

    return $this->render('member/show.html.twig', [
        'member' => $member,
    ]);
}


    
    
}
