<?php

namespace App\Service;

use LogicException;
use InvalidArgumentException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Mime\Exception\LogicException as ExceptionLogicException;

class FileUploaderHelper extends AbstractController
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    /**
     * @return string fileName : original name with uniqId
     */
    public function upload(UploadedFile $file)
    {
        $originalFilename = \pathinfo($file->getClientOriginalName(), \PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '-' . \uniqid() . '.' . $file->guessExtension();
        try {
            $file->move($this->getParameter('img_directory'), $fileName);
        } catch (FileException $e) {
            throw new \Exception("Un problème est survenu lors du téléchargement de l'image.");
        }

        return $fileName;
    }
}
