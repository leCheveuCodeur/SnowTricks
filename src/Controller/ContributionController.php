<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Contribution;
use App\Form\ContributionType;
use Symfony\Component\Form\Form;
use App\Service\FileUploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Test\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ContributionController extends AbstractController
{
    // /**
    //  * @Route("/contribuer/trick/{id?<\d+>}", name="contribution_edit_trick")
    //  */
    // public function editTrick(?Trick $trick = \null, Request $request, EntityManagerInterface $em, FileUploaderHelper $fileUploaderHelper): Response
    // {
    //     // \dd($request);
    //     \dump($request);
    //     $contribution = new Contribution;
    //     if ($trick) {
    //         $contribution->setTrick($trick);

    //         if (!$request->request->get('contribution')) {
    //             /** @var Image $image */
    //             foreach ($trick->getImages() as $image) {
    //                 $imageCopy = new Image;
    //                 $imageCopy->setImageTarget($image->getId())
    //                     ->setTitle($image->getTitle())
    //                     ->setPath($image->getPath())
    //                     ->setFileName($image->getFileName());
    //                 $contribution->addImage($imageCopy);
    //             }
    //         } else {
    //             $imagesInTheRequest = (array) $request->request->get('contribution')['images'];

    //             \dump("Start images dans la Request", $imagesInTheRequest);

    //             /** @var Image $image */
    //             foreach ($trick->getImages() as $key => $image) {
    //                 if ($key === \array_keys($imagesInTheRequest)[$key]) {
    //                     $imageCopy = new Image;
    //                     $imageCopy->setImageTarget($image->getId())
    //                         ->setTitle($image->getTitle())
    //                         ->setPath($image->getPath())
    //                         ->setFileName($image->getFileName());
    //                     $contribution->addImage($imageCopy);
    //                 }
    //             }
    //         }
    //     }

    //     // Remove from the request the image previously deleted but kept in memory by Symfony
    //     if ($request->request->get('contribution')) {
    //         $contributionRequestCopy = $request->request->all('contribution');
    //         $imagesInTheRequest = (array)$contributionRequestCopy['images'];
    //         $oldKey = 0;
    //         foreach ($imagesInTheRequest as $key => $image) {
    //             if ($key < $oldKey) {
    //                 unset($imagesInTheRequest[$key]);
    //             }
    //             $oldKey = $key;
    //         }
    //         $contributionRequestCopy['images'] = $imagesInTheRequest;
    //         $request->request->set('contribution', $contributionRequestCopy);
    //     }

    //     $formView = $this->createForm(ContributionType::class, $contribution);
    //     \dump("FormView de départ", $formView);


    //     // if ($request->request->get('contribution')) {
    //     //     $contribution->setTrick($trick);
    //     //     $imagesInTheRequest = (array) $request->request->get('contribution')['images'];


    //     //     $oldKey = '';
    //     //     foreach ($imagesInTheRequest as $key => $image) {
    //     //         if ($key > $oldKey) {
    //     //             $formView->get('images')->offsetUnset($key);
    //     //         }
    //     //         $oldKey = $key;
    //     //     }


    //     //         $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
    //     //         $normalizer = new ObjectNormalizer($classMetadataFactory);
    //     //         $serializer = new Serializer([$normalizer]);
    //     //         $image = $serializer->denormalize($image, Image::class, null, ['groups' => 'trick:read']);
    //     //         \dump($image);

    //     //         $contribution->addImage($image);

    //     //         $oldKey = $key;
    //     //     }
    //     // }

    //     //

    //     \dump($formView);

    //     // view the SubmitEvent ImageType
    //     // (adds in each image the path_name that corresponds to the file_name)
    //     $formView->handleRequest($request);

    //     \dump($formView);
    //     /** @var Form */
    //     $imagesForm = $formView->get('images');
    //     \dump($imagesForm);
    //     if ($request->request->get('contribution')) {
    //         \dump("contribution", $contribution, "request", $request);

    //         $contributionFilesCopy = $request->files->all('contribution');
    //         // ajou dans
    //         foreach ($imagesInTheRequest as $key => $imageInTheRequest) {
    //             if (\array_key_exists('file_name', $imageInTheRequest)) {
    //                 /** @var Image */
    //                 $image = $imagesForm->get($key)->getData();

    //                 $image->setPath(\preg_filter("/-\w+(?=\.\w+$)/", '', $imageInTheRequest['file_name']))
    //                     ->setFileName($imageInTheRequest['file_name']);

    //                 $fileUpload = new UploadedFile($image->getPath(), $image->getPath(), null, null, \false);

    //                 $imagesInTheRequest = (array)$contributionFilesCopy['images'];

    //                 $contributionFilesCopy['images'] = $imagesInTheRequest;
    //                 $request->files->set('contribution', $contributionFilesCopy);
    //             }
    //         }
    //     }

    //     if ($formView->isSubmitted() && $formView->isValid()) {

    //         $contribution->setDate()
    //             ->setUser($this->getUser());

    //         \dd($contribution);

    //         $em->persist($contribution);
    //         $em->flush();

    //         return $this->redirect($this->generateUrl('contribution_edit_trick'));
    //     }

    //     return $this->renderForm('contribution/edit_trick.html.twig', [
    //         'formView' => $formView,
    //         'trick' => $trick,
    //         'contribution' => $contribution, 'request' => $request
    //     ]);
    // }



    /**
     * @Route("/contribuer/trick/{id?<\d+>}", name="contribution_edit_trick")
     */
    public function editTrick(?Trick $trick = \null, Request $request, EntityManagerInterface $em, FileUploaderHelper $fileUploaderHelper): Response
    {
        \dump("Modèle avec telechargement AVANT Validation lors de la Soumission", $request);
        // \dump("Version sans FileName : Modèle avec telechargement AVANT Validation lors de la Soumission", $request);
        // \dump("Modèle avec telechargement AVANT Validation lors de la Soumission || ETAPE 2 : completion du champ upload oublié");
        // \dump("Version sans FileName : Modèle avec telechargement AVANT Validation lors de la Soumission || ETAPE 2 : completion du champ upload oublié");
        $contribution = new Contribution;
        if ($trick) {
            $contribution->setTrick($trick);

            // Init les Images existante en les ajoutant dans la Contribution
            if (!$request->request->get('contribution')) {
                /** @var Image $image */
                foreach ($trick->getImages() as $image) {
                    $imageCopy = new Image;
                    $imageCopy->setImageTarget($image->getId())
                        ->setTitle($image->getTitle())
                        ->setPath($image->getPath())
                        ->setFileName($image->getFileName());
                    $contribution->addImage($imageCopy);
                }
            } else {
                $imagesInTheRequest = (array) $request->request->get('contribution')['images'];

                \dump("Start images dans la Request", $imagesInTheRequest, "Start fichiers dans la Request", (array) $request->files->get('contribution')['images']);

                /** @var Image $image */
                foreach ($trick->getImages() as $key => $image) {
                    if ($key === \array_keys($imagesInTheRequest)[$key]) {
                        $imageCopy = new Image;
                        $imageCopy->setImageTarget($image->getId())
                            ->setTitle($image->getTitle())
                            ->setPath($image->getPath())
                            ->setFileName($image->getFileName());
                        $contribution->addImage($imageCopy);
                    }
                }
            }
        }
        // Fin de l'Intitulé
        //-------------------------------------------

        \dump($contribution);

        if ($request->request->get('contribution')) {
            $contributionRequestCopy = $request->request->all('contribution');
            $imagesInTheRequest = (array)$contributionRequestCopy['images'];
            $oldKey = 0;
            foreach ($imagesInTheRequest as $key => $image) {
                // Remove from the request the image previously deleted but kept in memory by Symfony
                if ($key < $oldKey) {
                    unset($imagesInTheRequest[$key]);
                }

                $oldKey = $key;
            }
            $contributionRequestCopy['images'] = $imagesInTheRequest;
            // Ecrase la request existante par notre copy
            $request->request->set('contribution', $contributionRequestCopy);
        }

        $formView = $this->createForm(ContributionType::class, $contribution);
        \dump("FormView de départ", $formView);

        // if ($request->request->get('contribution')) {
        //     $contribution->setTrick($trick);
        //     $imagesInTheRequest = (array) $request->request->get('contribution')['images'];


        //     $oldKey = '';
        //     foreach ($imagesInTheRequest as $key => $image) {
        //         if ($key > $oldKey) {
        //             $formView->get('images')->offsetUnset($key);
        //         }
        //         $oldKey = $key;
        //     }


        //         $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        //         $normalizer = new ObjectNormalizer($classMetadataFactory);
        //         $serializer = new Serializer([$normalizer]);
        //         $image = $serializer->denormalize($image, Image::class, null, ['groups' => 'trick:read']);
        //         \dump($image);

        //         $contribution->addImage($image);

        //         $oldKey = $key;
        //     }
        // }

        //
        \dump($contribution);
        // view the SubmitEvent ImageType
        // (adds in each image the path_name that corresponds to the file_name)
        $formView->handleRequest($request);
        \dump($formView, $contribution);

        /** @var Form */
        $imagesForm = $formView->get('images');
        \dump($imagesForm);

        // 2eme ETAPE - Recuperation des images dejà soumise lors des submit précédent mais ayant des erreurs
        if ($request->request->get('contribution')) {
            $contributionRequestCopy = $request->request->all('contribution');
            $imagesInTheRequest = (array)$contributionRequestCopy['images'];
            foreach ($imagesInTheRequest as $key => $imageInTheRequest) {
                if (array_key_exists('file_name', $imageInTheRequest) && \file_exists('img_uploads/' . $imageInTheRequest['file_name'])) {
                    /** @var Image */
                    $image = $imagesForm->get($key)->getData();

                    $image->setPath(\preg_filter("/-\w+(?=\.\w+$)/", '', $imageInTheRequest['file_name']))
                        ->setFileName($imageInTheRequest['file_name']);
                }
            }
        }
        \dump($imagesForm, $contribution);

        // if ($request->request->get('contribution')) {

        //     // $contributionFilesCopy = $request->files->all('contribution');
        //     //ajou dans
        // foreach ($imagesInTheRequest as $key => $imageInTheRequest) {
        //     if (\array_key_exists('file_name', $imageInTheRequest)) {
        //         /** @var Image */
        //         $image = $imagesForm->get($key)->getData();

        //         $image->setPath(\preg_filter("/-\w+(?=\.\w+$)/", '', $imageInTheRequest['file_name']))
        //             ->setFileName($imageInTheRequest['file_name']);

        //             // $fileUpload = new UploadedFile($image->getPath(), $image->getPath(), null, null, \false);

        //             // $imagesInTheRequest = (array)$contributionFilesCopy['images'];

        //             // $contributionFilesCopy['images'] = $imagesInTheRequest;
        //             // $request->files->set('contribution', $contributionFilesCopy);
        //         }
        //     }
        // }
        if ($formView->isSubmitted()) {

            if ($request->files->get('contribution')) {
                // $request->files->remove('contribution');
                // $contributionRequestCopy = $request->files->all('contribution');
                // $imagesInTheRequest = (array)$contributionRequestCopy['images'];
                // $oldKey = 0;
                // foreach ($imagesInTheRequest as $key => $image) {
                //     // Remove from the request the image previously deleted but kept in memory by Symfony
                //     if ($key < $oldKey) {
                //         unset($imagesInTheRequest[$key]);
                //         $oldKey = $key;
                //         continue;
                //     }

                //     if (array_key_exists('file_name', $image)) {
                //     }


                //     $oldKey = $key;
                // }
                // $contributionRequestCopy['images'] = $imagesInTheRequest;
                // $request->request->set('contribution', $contributionRequestCopy);
            }
            // foreach ($request->files->get('contribution')['images'] as $key => $upload) {
            //     /** @var UploadedFile */
            //     $file = $upload['path'];
            //     if ($file) {
            //         $fileName = $fileUploaderHelper->upload($file);

            //         /** @var Image */
            //         $image = $imagesForm->get($key)->getData();
            //         $image->setFileName($fileName);
            //         \dump($image);
            //     }
            // }

            \dump("contribution", $contribution, "request", $request);
        }

        if ($formView->isSubmitted() && $formView->isValid()) {
            \dump('valid !!');
            $contribution->setDate()
                ->setUser($this->getUser());

            \dump("contribution", $contribution, "request", $request);
            // \dd($contribution);

            // $em->persist($contribution);
            // $em->flush();

            // return $this->redirect($this->generateUrl('contribution_edit_trick'));
        }

        return $this->renderForm('contribution/edit_trick.html.twig', [
            'formView' => $formView,
            'trick' => $trick,
            'contribution' => $contribution, 'request' => $request
        ]);
    }
}
