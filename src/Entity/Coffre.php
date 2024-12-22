<?php

namespace App\Entity;

use App\Repository\CoffreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Member;

#[ORM\Entity(repositoryClass: CoffreRepository::class)]
class Coffre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * @var Collection<int, Jeu>
     */
    #[ORM\OneToMany(targetEntity: Jeu::class, mappedBy: 'coffre')]
    private Collection $jeux;

    #[ORM\OneToOne(targetEntity: Member::class, inversedBy: 'coffre')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Member $membre = null;

    public function __construct()
    {
        $this->jeux = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Jeu>
     */
    public function getJeux(): Collection
    {
        return $this->jeux;
    }

    public function addJeux(Jeu $jeux): static
    {
        if (!$this->jeux->contains($jeux)) {
            $this->jeux->add($jeux);
            $jeux->setCoffre($this);
        }

        return $this;
    }

    public function removeJeux(Jeu $jeux): static
    {
        if ($this->jeux->removeElement($jeux)) {
            // set the owning side to null (unless already changed)
            if ($jeux->getCoffre() === $this) {
                $jeux->setCoffre(null);
            }
        }

        return $this;
    }

  

public function getMembre(): ?Member
{
    return $this->membre;
}

public function setMembre(?Member $membre): self
{
    $this->membre = $membre;

    return $this;
}

    

    public function __toString(): string
    {
        return $this->nom; // Retourne le nom du coffre comme représentation en chaîne
    }
    
}
