<?php

namespace App\Entity;

use App\Repository\FileRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FileRepository::class)
 */
class File
{
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
     * @ORM\Column(type="integer")
     */
    private $img;

    /**
     * @ORM\Column(type="integer")
     */
    private $video;

    /**
     * @ORM\Column(type="integer")
     */
    private $avatar;

    /**
     * @ORM\ManyToOne(targetEntity=Trick::class, inversedBy="files")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trick;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="files")
     */
    private $user;

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

    public function getImg(): ?int
    {
        return $this->img;
    }

    public function setImg(int $img): self
    {
        $this->img = $img;

        return $this;
    }

    public function getVideo(): ?int
    {
        return $this->video;
    }

    public function setVideo(int $video): self
    {
        $this->video = $video;

        return $this;
    }

    public function getAvatar(): ?int
    {
        return $this->avatar;
    }

    public function setAvatar(int $avatar): self
    {
        $this->avatar = $avatar;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
