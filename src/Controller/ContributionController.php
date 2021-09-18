<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Video;
use App\Entity\Contribution;
use App\Form\ContributionType;
use Symfony\Component\Form\Form;
use App\Service\FileUploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContributionController extends AbstractController
{
    /**
     * @Route("/contribuer/trick/{id?<\d+>}", name="contribution_edit_trick")
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
            $videosInTheRequest = \key_exists('videos', $contributionFilesCopy) ? (array)$contributionRequestCopy['videos'] : \false;
            if ($contributionFilesCopy) {
                $filesInTheRequest = (array)$contributionFilesCopy['images'];
            }

            if ($imagesInTheRequest) {
                $oldKey = -1;
                $index = 0;
                $reindexedImages = [];
                $reindexedFiles = [];
                foreach ($imagesInTheRequest as $key => $image) {
                    if ($key > $oldKey) {
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
                    if ($key > $oldKey) {
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
        \dump($request);
        $formView->handleRequest($request);
        if ($formView->isSubmitted()) {
            \dump($request);
        }

        \dump($formView, $contribution, $request);
        if ($formView->isSubmitted() && $formView->isValid()) {

            if ($formView->get('image_in_front')) {
                $newImageInFront = $formView->get('image_in_front')->getData();
            }

            /** @var Form */
            $imagesForm = $formView->get('images');

            foreach ($imagesForm as $key => $imageForm) {
                /** @var Image */
                $image = $imageForm->getData();
                /** @var UploadedFile */
                $hasFile = $request->files->get('contribution')['images'][$key]['file_name'] ?? false;

                \dd($newImageInFront, $contribution, $request);
                if ($hasFile) {
                    $fileName = $fileUploaderHelper->upload($hasFile);
                    $image->setFileName($fileName);
                }

                if ($newImageInFront) {
                    if ($newImageInFront === $image->getOriginalFileName()) {
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

            return $this->redirect($this->generateUrl('contribution_edit_trick'));
        }

        return $this->renderForm('contribution/edit_trick.html.twig', [
            'formView' => $formView,
            'trick' => $trick,
            'contribution' => $contribution, 'request' => $request
        ]);
    }
}
