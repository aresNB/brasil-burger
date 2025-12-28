<?php

namespace App\Entity;

use App\Repository\LigneCommandeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigneCommandeRepository::class)]
#[ORM\Table(name: 'lignes_commande')]
class LigneCommande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\Column(name: 'prixunitaire', type: 'decimal', precision: 10, scale: 2)]
    private ?string $prixUnitaire = null;

    #[ORM\Column(name: 'soustotal', type: 'decimal', precision: 10, scale: 2)]
    private ?string $sousTotal = null;

    #[ORM\Column(name: 'typeproduit', length: 20)]
    private ?string $typeProduit = null;

    #[ORM\Column(name: 'commandeid')]
    private ?int $commandeId = null;

    #[ORM\ManyToOne(targetEntity: Commande::class, inversedBy: 'lignesCommande')]
    #[ORM\JoinColumn(name: 'commandeid', referencedColumnName: 'id')]
    private ?Commande $commande = null;

    #[ORM\Column(name: 'burgerid', nullable: true)]
    private ?int $burgerId = null;

    #[ORM\ManyToOne(targetEntity: Burger::class)]
    #[ORM\JoinColumn(name: 'burgerid', referencedColumnName: 'id', nullable: true)]
    private ?Burger $burger = null;
    #[ORM\Column(name: 'menuid', nullable: true)]
    private ?int $menuId = null;

    #[ORM\ManyToOne(targetEntity: Menu::class)]
    #[ORM\JoinColumn(name: 'menuid', referencedColumnName: 'id', nullable: true)]
    private ?Menu $menu = null;

    #[ORM\Column(name: 'complementid', nullable: true)]
    private ?int $complementId = null;

    #[ORM\ManyToOne(targetEntity: Complement::class)]
    #[ORM\JoinColumn(name: 'complementid', referencedColumnName: 'id', nullable: true)]
    private ?Complement $complement = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;
        return $this;
    }

    public function getPrixUnitaire(): ?string
    {
        return $this->prixUnitaire;
    }

    public function setPrixUnitaire(string $prixUnitaire): static
    {
        $this->prixUnitaire = $prixUnitaire;
        return $this;
    }

    public function getSousTotal(): ?string
    {
        return $this->sousTotal;
    }

    public function setSousTotal(string $sousTotal): static
    {
        $this->sousTotal = $sousTotal;
        return $this;
    }

    public function getTypeProduit(): ?string
    {
        return $this->typeProduit;
    }

    public function setTypeProduit(string $typeProduit): static
    {
        $this->typeProduit = $typeProduit;
        return $this;
    }

    public function getCommandeId(): ?int
    {
        return $this->commandeId;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): static
    {
        $this->commande = $commande;
        if ($commande !== null) {
            $this->commandeId = $commande->getId();
        }
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
        } else {
            $this->burgerId = null;
        }
        return $this;
    }

    public function getMenuId(): ?int
    {
        return $this->menuId;
    }

    public function getMenu(): ?Menu
    {
        return $this->menu;
    }

    public function setMenu(?Menu $menu): static
    {
        $this->menu = $menu;
        if ($menu !== null) {
            $this->menuId = $menu->getId();
        } else {
            $this->menuId = null;
        }
        return $this;
    }

    public function getComplementId(): ?int
    {
        return $this->complementId;
    }

    public function getComplement(): ?Complement
    {
        return $this->complement;
    }

    public function setComplement(?Complement $complement): static
    {
        $this->complement = $complement;
        if ($complement !== null) {
            $this->complementId = $complement->getId();
        } else {
            $this->complementId = null;
        }
        return $this;
    }
}
