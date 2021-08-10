<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Contribution;
use App\Service\FileUploaderHelper;
use App\Form\ContributionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Response;

class ContributionController extends AbstractController
{
    /**
     * @Route("/contribuer/trick/{id?<\d+>}", name="contribution_edit_trick")
     */
    public function editTrick(?Trick $trick = \null, Request $request, EntityManagerInterface $em, FileUploaderHelper $fileUploaderHelper): Response
    {
        $contribution = new Contribution;
        if ($trick !== null) {
            $contribution->setTrick($trick);
        }

        $formView = $this->createForm(ContributionType::class, $contribution);

        $formView->handleRequest($request);

        if ($formView->isSubmitted() && $formView->isValid()) {
            /**@var Form */
            $imagesForm = $formView->get('images');


            foreach ($imagesForm as $imageForm) {
                $imageFile = $imageForm->get('path')->getData();
                $imageFileName = $fileUploaderHelper->upload($imageFile);

                /**@var Image */
                $image = $imageForm->getData();
                $image->setPath($imageFileName);
            }

            $contribution->setDate()
                ->setUser($this->getUser());
            $em->persist($contribution);
            $em->flush();

            return $this->redirect($this->generateUrl('contribution_edit_trick'));
        }

        $formView = $formView->createView();

        return $this->render('contribution/edit_trick.html.twig', [
            'formView' => $formView, 'trick' => $trick
        ]);
    }
}
