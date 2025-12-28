<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
#[ORM\Table(name: 'menus')]
class Menu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $libelle = null;

    #[ORM\Column(name: 'imageurl', length: 500, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\Column(name: 'isarchived', type: 'boolean')]
    private ?bool $isArchived = false;

    #[ORM\Column(name: 'burgerid')]
    private ?int $burgerId = null;

    #[ORM\ManyToOne(targetEntity: Burger::class)]
    #[ORM\JoinColumn(name: 'burgerid', referencedColumnName: 'id')]
    private ?Burger $burger = null;

    #[ORM\Column(name: 'boissonid')]
    private ?int $boissonId = null;

    #[ORM\ManyToOne(targetEntity: Complement::class)]
    #[ORM\JoinColumn(name: 'boissonid', referencedColumnName: 'id')]
    private ?Complement $boisson = null;

    #[ORM\Column(name: 'friteid')]
    private ?int $friteId = null;

    #[ORM\ManyToOne(targetEntity: Complement::class)]
    #[ORM\JoinColumn(name: 'friteid', referencedColumnName: 'id')]
    private ?Complement $frite = null;

    #[ORM\Column(name: 'createdat', type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->isArchived = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;
        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    public function isArchived(): ?bool
    {
        return $this->isArchived;
    }

    public function setIsArchived(bool $isArchived): static
    {
        $this->isArchived = $isArchived;
        return $this;
    }

    public function getBurgerId(): ?int
    {
        return $this->burgerId;
    }

    public function getBurger(): ?Burger
    {
        return $this->burger;
    }

    public function setBurger(?Burger $burger): static
    {
        $this->burger = $burger;
        if ($burger !== null) {
            $this->burgerId = $burger->getId();
        }
        return $this;
    }

    public function getBoissonId(): ?int
    {
        return $this->boissonId;
    }

    public function getBoisson(): ?Complement
    {
        return $this->boisson;
    }

    public function setBoisson(?Complement $boisson): static
    {
        $this->boisson = $boisson;
        if ($boisson !== null) {
            $this->boissonId = $boisson->getId();
        }
        return $this;
    }

    public function getFriteId(): ?int
    {
        return $this->friteId;
    }

    public function getFrite(): ?Complement
    {
        return $this->frite;
    }

    public function setFrite(?Complement $frite): static
    {
        $this->frite = $frite;
        if ($frite !== null) {
            $this->friteId = $frite->getId();
        }
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getPrixTotal(): float
    {
        $total = 0;
        if ($this->burger) {
            $total += (float)$this->burger->getPrix();
        }
        if ($this->boisson) {
            $total += (float)$this->boisson->getPrix();
        }
        if ($this->frite) {
            $total += (float)$this->frite->getPrix();
        }
        return $total;
    }
}
