<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ImageRepository;
use Symfony\Component\Serializer\Annotation\Groups;
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
    private $file_name;

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

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("trick:read")
     */
    private $imageTarget;

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

    public function getImageTarget(): ?int
    {
        return $this->imageTarget;
    }

    public function setImageTarget(?int $imageTarget): self
    {
        $this->imageTarget = $imageTarget;

        return $this;
    }

    public function getFileName(): ?string
    {
        return $this->file_name;
    }

    public function setFileName(string $file_name): self
    {
        $this->file_name = $file_name;

        return $this;
    }
}
