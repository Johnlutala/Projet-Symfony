<?php

namespace App\Entity;

use App\Repository\RecettesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity('name')]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: RecettesRepository::class)]
class Recettes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank()]
    #[Assert\Length(min:2,max:50, minMessage:"Caractère Minimal vaut à 2", maxMessage:"Caractère Minimal vaut à 50")]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?int $time = null;

    #[ORM\Column(nullable: true)]
     #[Assert\Positive()]
    #[Assert\LessThan(6)]
    private ?int $difficulty = null;

    #[ORM\Column(nullable: true)]
     #[Assert\Positive()]
    private ?int $nbrepersonne = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Positive()]
    #[Assert\LessThan(1001)]
    private ?float $price = null;

    #[ORM\Column]
    private ?bool $isfavorite;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotNull()]
    private ?\DateTimeImmutable $creatDat = null;

    /**
     * @var Collection<int, ingredients>
     */
    #[ORM\ManyToMany(targetEntity: Ingredients::class)]
    private Collection $ingredients;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $updateDat = null;

    #[ORM\ManyToOne(inversedBy: 'recettes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?bool $IsPublic = null;

    public function __construct()
    {
        $this->ingredients = new ArrayCollection();
        $this->creatDat=new \DateTimeImmutable();
        $this->updateDat=new \DateTimeImmutable();
        $this->isfavorite=false;
    }

    #[ORM\PrePersist()]
    public function setupdateValue()
    {
        $this->updateDat=new \DateTimeImmutable();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getTime(): ?int
    {
        return $this->time;
    }

    public function setTime(?int $time): static
    {
        $this->time = $time;

        return $this;
    }

    public function getDifficulty(): ?int
    {
        return $this->difficulty;
    }

    public function setDifficulty(?int $difficulty): static
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    public function getNbrepersonne(): ?int
    {
        return $this->nbrepersonne;
    }

    public function setNbrepersonne(?int $nbrepersonne): static
    {
        $this->nbrepersonne = $nbrepersonne;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function isfavorite(): ?bool
    {
        return $this->isfavorite;
    }

    public function setIsfavorite(bool $isfavorite): static
    {
        $this->isfavorite = $isfavorite;

        return $this;
    }

    public function getCreatDat(): ?\DateTimeImmutable
    {
        return $this->creatDat;
    }

    public function setCreatDat(\DateTimeImmutable $creatDat): static
    {
        $this->creatDat = $creatDat;

        return $this;
    }

    /**
     * @return Collection<int, ingredients>
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }


    public function addIngredient(Ingredients $ingredient): static
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients->add($ingredient);
        }

        return $this;
    }

    public function removeIngredient(Ingredients $ingredient): static
    {
        $this->ingredients->removeElement($ingredient);

        return $this;
    }

    public function getUpdateDat(): ?\DateTimeImmutable
    {
        return $this->updateDat;
    }

    public function setUpdateDat(\DateTimeImmutable $updateDat): static
    {
        $this->updateDat = $updateDat;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function isPublic(): ?bool
    {
        return $this->IsPublic;
    }
     public function getIsPublic(): ?bool
    {
        return $this->IsPublic;
    }

    public function setIsPublic(bool $IsPublic): static
    {
        $this->IsPublic = $IsPublic;

        return $this;
    }
}
