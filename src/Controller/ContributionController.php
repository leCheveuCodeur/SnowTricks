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
                    ->setTitle($image->getTitle())
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

        // Remove from the request the image previously deleted but kept in memory by Symfony
        // and reindexing the request
        if ($request->request->get('contribution')) {
            $contributionRequestCopy = $request->request->all('contribution');
            $contributionFilesCopy = $request->files->all('contribution');

            $imagesInTheRequest = (array)$contributionRequestCopy['images'];
            $videosInTheRequest = (array)$contributionRequestCopy['videos'];
            if ($contributionFilesCopy) {
                $filesInTheRequest = (array)$contributionFilesCopy['images'];
            }

            $oldKey = -1;
            $index = 0;
            $reindexedImages = [];
            $reindexedFiles = [];
            foreach ($imagesInTheRequest as $key => $image) {
                if ($key > $oldKey) {
                    $reindexedImages[] = $image;
                    if ($contributionFilesCopy && key_exists($key, $filesInTheRequest)) {
                        $reindexedFiles[$index] = $filesInTheRequest[$key];
                    }
                }

                $oldKey = $key;
                $index++;
            }

            $oldKey = -1;
            $index = 0;
            $reindexedVideos = [];
            foreach ($videosInTheRequest as $key => $video) {
                if ($key > $oldKey) {
                    $reindexedVideos[] = $video;
                }

                $oldKey = $key;
                $index++;
            }

            $contributionRequestCopy['images'] = $reindexedImages;
            $contributionRequestCopy['videos'] = $reindexedVideos;
            $contributionFilesCopy['images'] = $reindexedFiles;
            // Overwrite the existing request with our copy
            $request->request->set('contribution', $contributionRequestCopy);
            $request->files->set('contribution', $contributionFilesCopy);
        }

        // End of reindexing the request
        //--------------------------------------------------------------------------------------------------------------------------------

        $formView = $this->createForm(ContributionType::class, $contribution);
        $formView->handleRequest($request);

        if ($formView->isSubmitted() && $formView->isValid()) {

            \dd($formView, $contribution, $request);

            /** @var Form */
            $imagesForm = $formView->get('images');

            foreach ($request->files->get('contribution')['images'] as $key => $upload) {
                /** @var UploadedFile */
                $file = $upload['file_name'];
                if ($file) {
                    $fileName = $fileUploaderHelper->upload($file);

                    /** @var Image */
                    $image = $imagesForm->get($key)->getData();
                    $image->setFileName($fileName);
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
