<?php

namespace App\Entity;

use App\Repository\QuartierRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuartierRepository::class)]
#[ORM\Table(name: 'quartiers')]
class Quartier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(name: 'codepostal', length: 10, nullable: true)]
    private ?string $codePostal = null;

    #[ORM\Column(name: 'zoneid')]
    private ?int $zoneId = null;

    #[ORM\ManyToOne(targetEntity: Zone::class, inversedBy: 'quartiers')]
    #[ORM\JoinColumn(name: 'zoneid', referencedColumnName: 'id')]
    private ?Zone $zone = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(?string $codePostal): static
    {
        $this->codePostal = $codePostal;
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
        }
        return $this;
    }
}
