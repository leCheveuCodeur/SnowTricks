<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\VideoRepository;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=VideoRepository::class)
 */
class Video
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("trick:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("trick:read")
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("trick:read")
     */
    private $link;

    /**
     * @ORM\ManyToOne(targetEntity=Trick::class, inversedBy="videos")
     */
    private $trick;

    /**
     * @ORM\ManyToOne(targetEntity=Contribution::class, inversedBy="videos")
     */
    private $contribution;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("trick:read")
     */
    private $videoTarget;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

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

    public function getVideoTarget(): ?int
    {
        return $this->videoTarget;
    }

    public function setVideoTarget(?int $videoTarget): self
    {
        $this->videoTarget = $videoTarget;

        return $this;
    }
}
