<?php

namespace App\Doctrine\Listener;

use App\Entity\Trick;
use Symfony\Component\String\Slugger\SluggerInterface;

class TrickSlugListener
{
    protected $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function prePersist(Trick $entity)
    {
        if (empty($entity->getSlug())) {
            $entity->setSlug(\strtolower($this->slugger->slug($entity->getTitle())));
        }
    }
}
