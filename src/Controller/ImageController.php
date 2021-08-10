<?php

namespace App\Controller;

// use App\Entity\Image;
// use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Image;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Symfony\Component\HttpFoundation\JsonResponse;
// use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Component\Validator\Constraints\Json;

class ImageController extends AbstractController
{
    //     /**
    //      * @Route("/image/supprimer/{id}", name="delete_image", methods={"DELETE"})
    //      */
    //     public function deleteImage(Image $image, Request $request, EntityManagerInterface $em)
    //     {
    //         $data = \json_decode($request->getContent(), \true);

    //         if ($this->isCsrfTokenValid('delete' . $image->getId(), $data['_token'])) {
    //             $path = $image->getPath();
    //             \unlink($this->getParameter('img_directory') . '/' . $path);

    //             $em->remove($image);
    //             $em->flush();

    //             return new JsonResponse(['success' => 1]);
    //         } else {
    //             return new JsonResponse(['error' => 'Token Invalide'], 400);
    //         }
    //     }

    /**
     *@Route("/image/ajouter",name="add_image")
     */
    public function addImage(Request $request)
    {
        $img=new Image;

    }
}
