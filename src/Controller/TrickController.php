<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickController extends AbstractController
{
    /**
     * @Route("/trick/{id<\d+>}/{page<\d+>?1}", name="trick_view")
     */
    public function viewTrick(Trick $trick, ?string $page, Request $request, EntityManagerInterface $em, CommentRepository $commentRepository): Response
    {
        $formViewComment = $this->createForm(CommentType::class);
        $formViewComment->handleRequest($request);

        $paginatedComments = $commentRepository->getLoadMoreComments($trick->getId(), $page, 5);

        if ($formViewComment->isSubmitted() && $formViewComment->isValid()) {
            /** @var Comment */
            $comment = $formViewComment->getData();
            $comment->setDate(new DateTime())
                ->setTrick($trick);

            // \dd($formViewComment, $comment, $request);
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
}
