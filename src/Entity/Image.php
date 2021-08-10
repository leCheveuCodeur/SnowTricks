<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 */
class Image
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(max=255, maxMessage="Le titre de l'image ne doit pas dépasser {{ limit }} caractères")
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(max=255, maxMessage="Le nom du fichier ne doit pas dépasser {{ limit }} caractères")
     */
    private $path;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $in_front;

    /**
     * @ORM\ManyToOne(targetEntity=Trick::class, inversedBy="images")
     */
    private $trick;

    /**
     * @ORM\ManyToOne(targetEntity=Contribution::class, inversedBy="images")
     */
    private $contribution;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title = \null): self
    {
        if (empty($title)) {
            $slugger = new AsciiSlugger('fr_Fr');
            $title = $slugger->slug(\preg_replace("/\.\w+$/", '', $this->path), ' ');
        }

        $this->title = $title;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getInFront(): ?int
    {
        return $this->in_front;
    }

    public function setInFront(?int $in_front): self
    {
        $this->in_front = $in_front;

        return $this;
    }

    public function getTrick(): ?Trick
    {
        return $this->trick;
    }

    public function setTrick(?Trick $trick): self
    {
        $this->trick = $trick;

        return $this;
    }

    public function getContribution(): ?Contribution
    {
        return $this->contribution;
    }

    public function setContribution(?Contribution $contribution): self
    {
        $this->contribution = $contribution;

        return $this;
    }
}
