<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VilleRepository::class)]
class Ville
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $nom = null;

    #[ORM\Column]
    #[Assert\Regex('/\d{2}[ ]?\d{3}/')]
    private ?int $codePostal = null;

    #[ORM\OneToMany(mappedBy: 'ville', targetEntity: Lieu::class)]
    private Collection $lieu;

    public function __construct()
    {
        $this->lieu = new ArrayCollection();
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

    public function getCodePostal(): ?int
    {
        return $this->codePostal;
    }

    public function setCodePostal(int $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * @return Collection<int, Lieu>
     */
    public function getLieu(): Collection
    {
        return $this->lieu;
    }

    public function addLieu(Lieu $lieu): self
    {
        if (!$this->lieu->contains($lieu)) {
            $this->lieu->add($lieu);
            $lieu->setVille($this);
        }

        return $this;
    }

    public function removeLieu(Lieu $lieu): self
    {
        if ($this->lieu->removeElement($lieu)) {
            // set the owning side to null (unless already changed)
            if ($lieu->getVille() === $this) {
                $lieu->setVille(null);
            }
        }

        return $this;
    }
}
