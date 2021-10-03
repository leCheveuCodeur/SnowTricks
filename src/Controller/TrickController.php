<?php

namespace App\Controller;

use DateTime;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\TrickRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickController extends AbstractController
{
    /**
     * @Route("/{page<\d+>?1}", name="homepage")
     */
    public function allTricks(TrickRepository $trickRepository, ?string $page): Response
    {
        $tricks = $trickRepository->findAll();

        $limit = 8;
        $paginatedTricks = $trickRepository->getLoadMoreTricks($page, $limit);

        if ($page > 1) {
            return new JsonResponse([
                'content' => $this->renderView('trick/_tricks.html.twig', ['page' => $page, 'paginatedTricks' => $paginatedTricks]),
                'endOfCollection' => (($page * $limit) > count($tricks))
            ]);
        }

        return $this->render('trick/homepage.html.twig', [
            'paginatedTricks' => $paginatedTricks,
            'endOfCollection' => (($page * $limit) > count($tricks))
        ]);
    }

    /**
     * @Route("/trick/{slug}/{page<\d+>?1}", name="trick_view")
     */
    public function viewTrick(Trick $trick, ?string $page, Request $request, EntityManagerInterface $em, CommentRepository $commentRepository): Response
    {
        $formViewComment = $this->createForm(CommentType::class);
        $formViewComment->handleRequest($request);

        $limit = 5;
        $paginatedComments = $commentRepository->getLoadMoreComments($trick->getId(), $page, $limit);

        if ($page > 1) {
            return new JsonResponse([
                'content' => $this->renderView('trick/_comments.html.twig', ['trick' => $trick, 'page' => $page, 'paginatedComments' => $paginatedComments]),
                'endOfCollection' => (($page * $limit) > count($trick->getComments()))
            ]);
        }

        if ($formViewComment->isSubmitted() && $formViewComment->isValid()) {
            /** @var Comment */
            $comment = $formViewComment->getData();
            $comment->setDate(new DateTime())
                ->setUser($this->getUser())
                ->setTrick($trick);

            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Ton commentaire à bien été ajouté !');
            return $this->redirect($request->getUri());
        }

        return $this->renderForm('trick/view_trick.html.twig', [
            'trick' => $trick,
            'formViewComment' => $formViewComment,
            'paginatedComments' => $paginatedComments,
            'page' => $page
        ]);
    }

    /**
     * @Route("/admin/trick/{id<\d+>}/suppression", name="trick_delete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteTrick(Trick $trick, EntityManagerInterface $em): Response
    {
        $em->remove($trick);
        $em->flush();

        $this->addFlash('success', 'Suppression réussie du Trick');

        return $this->redirectToRoute('homepage');
    }
}
