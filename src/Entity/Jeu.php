<?php

namespace App\Entity;

use App\Repository\JeuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: JeuRepository::class)]
#[Vich\Uploadable]
class Jeu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $annee = null;

    #[ORM\Column]
    private ?int $nb_joueurs_max = null;

    #[ORM\Column]
    private ?int $nb_joueurs_mini = null;

    #[ORM\Column]
    private ?int $temps_jeu = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageName = null;

    #[Vich\UploadableField(mapping: 'jeux_images', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'jeux')]
    private ?Coffre $coffre = null;


    /**
     * @var Collection<int, Bibliotheque>
     */
    #[ORM\ManyToMany(targetEntity: Bibliotheque::class, mappedBy: 'jeux')]
    private Collection $bibliotheques;

    public function __construct()
    {
        $this->bibliotheques = new ArrayCollection();
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

    public function getAnnee(): ?string
    {
        return $this->annee;
    }

    public function setAnnee(string $annee): static
    {
        $this->annee = $annee;

        return $this;
    }

    public function getNbJoueursMax(): ?int
    {
        return $this->nb_joueurs_max;
    }

    public function setNbJoueursMax(int $nb_joueurs_max): static
    {
        $this->nb_joueurs_max = $nb_joueurs_max;

        return $this;
    }

    public function getNbJoueursMini(): ?int
    {
        return $this->nb_joueurs_mini;
    }

    public function setNbJoueursMini(int $nb_joueurs_mini): static
    {
        $this->nb_joueurs_mini = $nb_joueurs_mini;

        return $this;
    }

    public function getTempsJeu(): ?int
    {
        return $this->temps_jeu;
    }

    public function setTempsJeu(int $temps_jeu): static
    {
        $this->temps_jeu = $temps_jeu;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

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


    public function getCoffre(): ?Coffre
    {
        return $this->coffre;
    }

    public function setCoffre(?Coffre $coffre): static
    {
        $this->coffre = $coffre;

        return $this;
    }

    /**
     * @return Collection<int, Bibliotheque>
     */
    public function getBibliotheques(): Collection
    {
        return $this->bibliotheques;
    }

    public function addBibliotheque(Bibliotheque $bibliotheque): static
    {
        if (!$this->bibliotheques->contains($bibliotheque)) {
            $this->bibliotheques->add($bibliotheque);
            $bibliotheque->addJeux($this);
        }

        return $this;
    }

    public function removeBibliotheque(Bibliotheque $bibliotheque): static
    {
        if ($this->bibliotheques->removeElement($bibliotheque)) {
            $bibliotheque->removeJeux($this);
        }

        return $this;
    }


    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}

