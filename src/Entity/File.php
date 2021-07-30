<?php

namespace App\Entity;

use App\Repository\FileRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FileRepository::class)
 */
class File
{
    const TYPE_IMG = 1;
    const TYPE_VIDEO = 2;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @ORM\ManyToOne(targetEntity=Trick::class, inversedBy="files")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trick;

    /**
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=Contribution::class, inversedBy="files")
     */
    private $contribution;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getTrick(): ?Trick
    {
        return $this->trick;
    }

    public function setTrick(?Trick $trick): self
    {
        $this->trick = $trick;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

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
