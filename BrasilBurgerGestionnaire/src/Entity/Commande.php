<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
#[ORM\Table(name: 'commandes')]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'numerocommande', length: 50)]
    private ?string $numeroCommande = null;

    #[ORM\Column(name: 'datecommande', type: 'datetime')]
    private ?\DateTimeInterface $dateCommande = null;

    #[ORM\Column(name: 'montanttotal', type: 'decimal', precision: 10, scale: 2)]
    private ?string $montantTotal = null;

    #[ORM\Column(length: 20)]
    private ?string $etat = 'EN_ATTENTE';

    #[ORM\Column(name: 'modeconsommation', length: 20)]
    private ?string $modeConsommation = null;

    #[ORM\Column(name: 'adresselivraison', type: 'text', nullable: true)]
    private ?string $adresseLivraison = null;

    #[ORM\Column(name: 'clientid')]
    private ?int $clientId = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'clientid', referencedColumnName: 'id')]
    private ?User $client = null;

    #[ORM\Column(name: 'livreurid', nullable: true)]
    private ?int $livreurId = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'livreurid', referencedColumnName: 'id', nullable: true)]
    private ?User $livreur = null;

    #[ORM\Column(name: 'zoneid', nullable: true)]
    private ?int $zoneId = null;

    #[ORM\ManyToOne(targetEntity: Zone::class)]
    #[ORM\JoinColumn(name: 'zoneid', referencedColumnName: 'id', nullable: true)]
    private ?Zone $zone = null;

    #[ORM\Column(name: 'createdat', type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(name: 'updatedat', type: 'datetime')]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\OneToMany(targetEntity: LigneCommande::class, mappedBy: 'commande')]
    private Collection $lignesCommande;

    public function __construct()
    {
        $this->lignesCommande = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->dateCommande = new \DateTime();
        $this->etat = 'EN_ATTENTE';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroCommande(): ?string
    {
        return $this->numeroCommande;
    }

    public function setNumeroCommande(string $numeroCommande): static
    {
        $this->numeroCommande = $numeroCommande;
        return $this;
    }

    public function getDateCommande(): ?\DateTimeInterface
    {
        return $this->dateCommande;
    }

    public function setDateCommande(\DateTimeInterface $dateCommande): static
    {
        $this->dateCommande = $dateCommande;
        return $this;
    }

    public function getMontantTotal(): ?string
    {
        return $this->montantTotal;
    }

    public function setMontantTotal(string $montantTotal): static
    {
        $this->montantTotal = $montantTotal;
        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getModeConsommation(): ?string
    {
        return $this->modeConsommation;
    }

    public function setModeConsommation(string $modeConsommation): static
    {
        $this->modeConsommation = $modeConsommation;
        return $this;
    }

    public function getAdresseLivraison(): ?string
    {
        return $this->adresseLivraison;
    }

    public function setAdresseLivraison(?string $adresseLivraison): static
    {
        $this->adresseLivraison = $adresseLivraison;
        return $this;
    }

    public function getClientId(): ?int
    {
        return $this->clientId;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): static
    {
        $this->client = $client;
        if ($client !== null) {
            $this->clientId = $client->getId();
        }
        return $this;
    }

    public function getLivreurId(): ?int
    {
        return $this->livreurId;
    }

    public function getLivreur(): ?User
    {
        return $this->livreur;
    }

    public function setLivreur(?User $livreur): static
    {
        $this->livreur = $livreur;
        if ($livreur !== null) {
            $this->livreurId = $livreur->getId();
        } else {
            $this->livreurId = null;
        }
        return $this;
    }

    public function getZoneId(): ?int
    {
        return $this->zoneId;
    }

    public function getZone(): ?Zone
    {
        return $this->zone;
    }

    public function setZone(?Zone $zone): static
    {
        $this->zone = $zone;
        if ($zone !== null) {
            $this->zoneId = $zone->getId();
        } else {
            $this->zoneId = null;
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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return Collection<int, LigneCommande>
     */
    public function getLignesCommande(): Collection
    {
        return $this->lignesCommande;
    }

    public function addLignesCommande(LigneCommande $lignesCommande): static
    {
        if (!$this->lignesCommande->contains($lignesCommande)) {
            $this->lignesCommande->add($lignesCommande);
            $lignesCommande->setCommande($this);
        }

        return $this;
    }

    public function removeLignesCommande(LigneCommande $lignesCommande): static
    {
        if ($this->lignesCommande->removeElement($lignesCommande)) {
            if ($lignesCommande->getCommande() === $this) {
                $lignesCommande->setCommande(null);
            }
        }

        return $this;
    }
}
