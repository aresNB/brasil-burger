<?php

namespace App\Entity;

use App\Repository\BurgerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BurgerRepository::class)]
#[ORM\Table(name: 'burgers')]
class Burger
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $libelle = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $prix = null;

    #[ORM\Column(name: 'imageurl', length: 500, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\Column(name: 'isarchived', type: 'boolean')]
    private ?bool $isArchived = false;

    #[ORM\Column(name: 'categorieid', nullable: true)]
    private ?int $categorieId = null;

    #[ORM\ManyToOne(targetEntity: BurgerCategorie::class, inversedBy: 'burgers')]
    #[ORM\JoinColumn(name: 'categorieid', referencedColumnName: 'id', nullable: true)]
    private ?BurgerCategorie $categorie = null;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): static
    {
        $this->prix = $prix;
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

    public function getCategorieId(): ?int
    {
        return $this->categorieId;
    }

    public function getCategorie(): ?BurgerCategorie
    {
        return $this->categorie;
    }

    public function setCategorie(?BurgerCategorie $categorie): static
    {
        $this->categorie = $categorie;
        if ($categorie !== null) {
            $this->categorieId = $categorie->getId();
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
}
