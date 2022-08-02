<?php

namespace App\Entity;

use App\Repository\CampusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CampusRepository::class)]
class Campus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\OneToMany(mappedBy: 'campus', targetEntity: Sortie::class)]
    private Collection $siteOrganisateur;

    #[ORM\OneToMany(mappedBy: 'campus', targetEntity: Participant::class)]
    private Collection $estRattacheA;

    public function __construct()
    {
        $this->siteOrganisateur = new ArrayCollection();
        $this->estRattacheA = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSiteOrganisateur(): Collection
    {
        return $this->siteOrganisateur;
    }

    public function addSiteOrganisateur(Sortie $siteOrganisateur): self
    {
        if (!$this->siteOrganisateur->contains($siteOrganisateur)) {
            $this->siteOrganisateur->add($siteOrganisateur);
            $siteOrganisateur->setCampus($this);
        }

        return $this;
    }

    public function removeSiteOrganisateur(Sortie $siteOrganisateur): self
    {
        if ($this->siteOrganisateur->removeElement($siteOrganisateur)) {
            // set the owning side to null (unless already changed)
            if ($siteOrganisateur->getCampus() === $this) {
                $siteOrganisateur->setCampus(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Participant>
     */
    public function getEstRattacheA(): Collection
    {
        return $this->estRattacheA;
    }

    public function addEstRattacheA(Participant $estRattacheA): self
    {
        if (!$this->estRattacheA->contains($estRattacheA)) {
            $this->estRattacheA->add($estRattacheA);
            $estRattacheA->setCampus($this);
        }

        return $this;
    }

    public function removeEstRattacheA(Participant $estRattacheA): self
    {
        if ($this->estRattacheA->removeElement($estRattacheA)) {
            // set the owning side to null (unless already changed)
            if ($estRattacheA->getCampus() === $this) {
                $estRattacheA->setCampus(null);
            }
        }

        return $this;
    }
}
