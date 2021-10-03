<?php

namespace App\Doctrine\Listener;

use App\Entity\Image;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImageRemoveListener
{
    public $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    public function preRemove(Image $image)
    {
        if (!$image->getImageTarget() && \file_exists($this->container->getParameter('img_directory') . '/' . $image->getFileName())) {
            \unlink($this->container->getParameter('img_directory') . '/' . $image->getFileName());
        }
    }
}
