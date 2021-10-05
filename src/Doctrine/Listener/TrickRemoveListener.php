<?php

namespace App\Doctrine\Listener;

use App\Entity\Trick;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TrickRemoveListener
{
    public $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    public function preRemove(Trick $trick)
    {
        foreach ($trick->getImages() as $image) {
            if (\file_exists($this->container->getParameter('img_directory') . '/' . $image->getFileName())) {
                \unlink($this->container->getParameter('img_directory') . '/' . $image->getFileName());
            }
        }
    }
}
