<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Video;
use App\Entity\Contribution;
use App\Form\ContributionType;
use App\Repository\ContributionRepository;
use App\Repository\TrickRepository;
use Symfony\Component\Form\Form;
use App\Service\FileUploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContributionController extends AbstractController
{
    /**
     * @Route("/admin/contribution/{id<\d+>}", name="contribution_view")
     * @IsGranted("ROLE_AUTHOR")
     */
    public function viewContribution(Contribution $contribution): Response
    {
        if (!$this->isGranted(User::ROLE_ADMIN) && $contribution->getTrick()->getAuthor()->getId() !== $this->getUser()->getId()) {
            return $this->redirectToRoute('homepage');
        }
        return $this->render('contribution/view_contribution.html.twig', [
            'contribution' => $contribution
        ]);
    }

    /**
     * @Route("/admin/contribution/{id<\d+>}/validation", name="contribution_validation")
     * @IsGranted("ROLE_AUTHOR")
     */
    public function validationContribution(Contribution $contribution, EntityManagerInterface $em, TrickRepository $trickRepository, ContributionRepository $contributionRepository): Response
    {
        if (!$this->isGranted(User::ROLE_ADMIN) && $contribution->getTrick()->getAuthor()->getId() !== $this->getUser()->getId()) {
            return $this->redirectToRoute('homepage');
        }
        if ($contribution->getTrick()) {
            $trick = $trickRepository->find($contribution->getTrick());

            $trick->addContributor($contribution->getUser())
                ->setLeadIn($contribution->getLeadIn())
                ->setContent($contribution->getContent())
                ->setModifiedDate($contribution->getDate());
            $trick->getImages()->clear();
            $trick->getVideos()->clear();
            /** @var Image $image */
            foreach ($contribution->getImages() as $image) {
                $copyImage = new Image;
                $copyImage->setTrick($contribution->getTrick())
                    ->setTitle($image->getTitle())
                    ->setFileName($image->getFileName())
                    ->setOriginalFileName($image->getOriginalFileName())
                    ->setInFront($image->getInFront());
                $trick->addImage($copyImage);
            }
            /** @var Video $video */
            foreach ($contribution->getVideos() as $video) {
                $copyVideo = new Video;
                $copyVideo->setTrick($contribution->getTrick())
                    ->setTitle($video->getTitle())
                    ->setLink($video->getLink());
                $trick->addVideo($copyVideo);
            }
        } else {
            $trick = new Trick;
            $trick->setAuthor($contribution->getUser())
                ->setTitle($contribution->getTitle())
                ->setLeadIn($contribution->getLeadIn())
                ->setContent($contribution->getContent())
                ->setCategory($contribution->getCategory())
                ->setCreationDate($contribution->getDate());
        }

        $em->persist($trick);
        $em->remove($contribution);
        $em->flush();

        $this->addFlash('success', 'Et voilà ! La Contribution à bien été prise en compte');

        return $this->redirectToRoute('user_admin_panel');
    }

    /**
     * @Route("/admin/contribution/{id<\d+>}/suppression", name="contribution_delete")
     * @IsGranted("ROLE_AUTHOR")
     */
    public function deleteContribution(Contribution $contribution, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted(User::ROLE_ADMIN) && $contribution->getTrick()->getAuthor()->getId() !== $this->getUser()->getId()) {
            return $this->redirectToRoute('homepage');
        }

        $em->remove($contribution);
        $em->flush();

        $this->addFlash('success', 'Suppression réussie de la Contribution');

        return $this->redirectToRoute('user_admin_panel');
    }


    /**
     * @Route("/contribuer/trick/{slug?}", name="contribution_new")
     * @IsGranted("ROLE_USER")
     */
    public function editTrick(?Trick $trick = \null, Request $request, FileUploaderHelper $fileUploaderHelper, EntityManagerInterface $em): Response
    {
        $contribution = new Contribution;
        //--------------------------------------------------------------------------------------------------------------------------------
        // Init existing Images by adding them in the Contribution
        if ($trick) {
            $contribution->setTrick($trick);

            /** @var Image $image */
            foreach ($trick->getImages() as $image) {
                $imageCopy = new Image;
                $imageCopy->setImageTarget($image->getId())
                    ->setInFront($image->getInFront())
                    ->setTitle($image->getTitle())
                    ->setOriginalFileName($image->getOriginalFileName())
                    ->setFileName($image->getFileName());
                $contribution->addImage($imageCopy);
            }

            /** @var Video $video */
            foreach ($trick->getVideos() as $video) {
                $videoCopy = new Video;
                $videoCopy->setVideoTarget($video->getId())
                    ->setTitle($video->getTitle())
                    ->setLink($video->getLink());
                $contribution->addVideo($videoCopy);
            }
        }
        // End of init
        //--------------------------------------------------------------------------------------------------------------------------------

        // Remove from the request the media previously deleted but kept in memory by Symfony
        // and reindexing the request
        if ($request->request->get('contribution')) {
            $contributionRequestCopy = $request->request->all('contribution');
            $contributionFilesCopy = $request->files->all('contribution');

            $imagesInTheRequest = \key_exists('images', $contributionRequestCopy) ? (array)$contributionRequestCopy['images'] : \false;
            $videosInTheRequest = \key_exists('videos', $contributionRequestCopy) ? (array)$contributionRequestCopy['videos'] : \false;

            if ($contributionFilesCopy) {
                $filesInTheRequest = (array)$contributionFilesCopy['images'];
            }

            if ($imagesInTheRequest) {
                $oldKey = -1;
                $index = 0;
                $reindexedImages = [];
                $reindexedFiles = [];
                foreach ($imagesInTheRequest as $key => $image) {
                    if ($key > $oldKey && \key_exists('title', $image)) {
                        $reindexedImages[] = $image;
                        \dump($image);
                        if ($contributionFilesCopy && key_exists($key, $filesInTheRequest)) {
                            $reindexedFiles[$index] = $filesInTheRequest[$key];
                        }
                    } else {
                        break;
                    }

                    $oldKey = $key;
                    $index++;
                }

                $contributionRequestCopy['images'] = $reindexedImages;
                $contributionFilesCopy['images'] = $reindexedFiles;
                // Overwrite the existing request with our copy
                $request->files->set('contribution', $contributionFilesCopy);
            }

            if ($videosInTheRequest) {
                $oldKey = -1;
                $index = 0;
                $reindexedVideos = [];
                foreach ($videosInTheRequest as $key => $video) {
                    if ($key > $oldKey && \key_exists('title', $video)) {
                        $reindexedVideos[] = $video;
                    } else {
                        break;
                    }

                    $oldKey = $key;
                    $index++;
                }

                $contributionRequestCopy['videos'] = $reindexedVideos;
            }

            if ($imagesInTheRequest || $videosInTheRequest) {
                // Overwrite the existing request with our copy
                $request->request->set('contribution', $contributionRequestCopy);
            }
        }

        // End of reindexing the request
        //--------------------------------------------------------------------------------------------------------------------------------

        $formView = $this->createForm(ContributionType::class, $contribution);
        $formView->handleRequest($request);

        if ($formView->isSubmitted() && $formView->isValid()) {
            if ($formView->get('image_in_front')) {
                $newImageInFront = intval($formView->get('image_in_front')->getData());
            }
            /** @var Form */
            $imagesForm = $formView->get('images');

            foreach ($imagesForm as $key => $imageForm) {
                /** @var Image */
                $image = $imageForm->getData();
                /** @var UploadedFile */
                $hasFile = $request->files->get('contribution')['images'][$key]['file_name'] ?? false;

                if ($hasFile) {
                    $fileName = $fileUploaderHelper->upload($hasFile);
                    $image->setFileName($fileName);
                }

                if (isset($newImageInFront)) {
                    if ($newImageInFront === $key) {
                        $image->setInFront(1);
                    } else {
                        $image->setInFront(\null);
                    }
                }
            }

            $contribution->setDate()
                ->setUser($this->getUser());

            $em->persist($contribution);
            $em->flush();

            $this->addFlash('success', 'Et voilà ! Ta Contribution a bien été soumise ! Merci !');

            return $this->redirect($this->generateUrl('homepage'));
        }

        return $this->renderForm('contribution/new_contribution.html.twig', [
            'formView' => $formView,
            'trick' => $trick,
            'contribution' => $contribution, 'request' => $request
        ]);
    }
}
