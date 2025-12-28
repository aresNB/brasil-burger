<?php

namespace App\Entity;

use App\Repository\PaiementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaiementRepository::class)]
#[ORM\Table(name: 'paiements')]
class Paiement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'datepaiement', type: 'datetime')]
    private ?\DateTimeInterface $datePaiement = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $montant = null;

    #[ORM\Column(name: 'moyenpaiement', length: 20)]
    private ?string $moyenPaiement = null;

    #[ORM\Column(name: 'reftransaction', length: 100, unique: true)]
    private ?string $refTransaction = null;

    #[ORM\Column(length: 20)]
    private ?string $statut = 'VALIDE';

    #[ORM\Column(name: 'commandeid')]
    private ?int $commandeId = null;

    #[ORM\ManyToOne(targetEntity: Commande::class)]
    #[ORM\JoinColumn(name: 'commandeid', referencedColumnName: 'id')]
    private ?Commande $commande = null;

    public function __construct()
    {
        $this->datePaiement = new \DateTime();
        $this->statut = 'VALIDE';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatePaiement(): ?\DateTimeInterface
    {
        return $this->datePaiement;
    }

    public function setDatePaiement(\DateTimeInterface $datePaiement): static
    {
        $this->datePaiement = $datePaiement;
        return $this;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): static
    {
        $this->montant = $montant;
        return $this;
    }

    public function getMoyenPaiement(): ?string
    {
        return $this->moyenPaiement;
    }

    public function setMoyenPaiement(string $moyenPaiement): static
    {
        $this->moyenPaiement = $moyenPaiement;
        return $this;
    }

    public function getRefTransaction(): ?string
    {
        return $this->refTransaction;
    }

    public function setRefTransaction(string $refTransaction): static
    {
        $this->refTransaction = $refTransaction;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
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
}
