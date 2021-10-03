<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ContributionRepository;
use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
     * @Route("/admin", name="user_admin_panel")
     * @IsGranted("ROLE_AUTHOR")
     */
    public function viewAdminPanel(TrickRepository $trickRepository, ContributionRepository $contributionRepository): Response
    {
        /** @var User */
        $user = $this->getUser();
        $userId = $user->getId();

        $tricks = $trickRepository->findAll();
        if ($this->isGranted(User::ROLE_ADMIN)) {
            /** @var Contribution[] */
            $newTricks = $contributionRepository->matching($contributionRepository::createNewTrickCriteria());
        }

        return $this->render('user/admin_panel.html.twig', [
            'user' => $user,
            'tricks' => $tricks,
            'newTricks' => $newTricks ?? false
        ]);
    }
}
