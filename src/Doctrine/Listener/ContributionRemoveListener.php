<?php

namespace App\Doctrine\Listener;

use App\Entity\Contribution;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContributionRemoveListener
{
    public $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    public function preRemove(Contribution $contribution)
    {
        foreach ($contribution->getImages() as $image) {

            if (!$image->getImageTarget() && \file_exists($this->container->getParameter('img_directory') . '/' . $image->getFileName())) {
                \unlink($this->container->getParameter('img_directory') . '/' . $image->getFileName());
            };
        }
    }
}
