<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[UniqueEntity('email')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
 #[ORM\EntityListeners(['App\EntityListener\UserListener'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column] 
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\Email()]
    #[Assert\Length(min:2,max:50, minMessage:"Caractère Minimal vaut à 2", maxMessage:"Caractère Minimal vaut à 180")]
    private ?string $email;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Assert\NotBlank()]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    private ?string $plainPassword=null;

    #[ORM\Column]
    #[Assert\NotBlank()]
    private ?string $password = 'password';

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank()]
    #[Assert\Length(min:2,max:50, minMessage:"Caractère Minimal vaut à 2", maxMessage:"Caractère Minimal vaut à 50")]
    private ?string $fullname;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(min:2,max:50, minMessage:"Caractère Minimal vaut à 2", maxMessage:"Caractère Minimal vaut à 50")]
    private ?string $pseudo = null;

    #[ORM\Column]
    #[Assert\NotBlank()]
    private ?\DateTimeImmutable $DateCreat;

    /**
     * @var Collection<int, Ingredients>
     */
    #[ORM\OneToMany(targetEntity: Ingredients::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $Ingredients;

    /**
     * @var Collection<int, Recettes>
     */
    #[ORM\OneToMany(targetEntity: Recettes::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $recettes;

    public function __construct()
    {
        $this->DateCreat= new \DateTimeImmutable();
        $this->roles= ["ROLE_USER"];
        $this->Ingredients = new ArrayCollection();
        $this->recettes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

     /**
  * Get the hashed password
  *
  * @return  string
  */ 
 public function getPlainPassword()
                                     {
                                         return $this->plainPassword;
                                     }
 /**
  * Set the hashed password
  *
  * @param  string  $plainPassword  The hashed password
  *
  * @return  self
  */ 
 public function setPlainPassword(string $plainPassword)
                                     {
                                         $this->plainPassword = $plainPassword;
                                         return $this;
                                     }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): static
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(?string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getDateCreat(): ?\DateTimeImmutable
    {
        return $this->DateCreat;
    }

    public function setDateCreat(\DateTimeImmutable $DateCreat): static
    {
        $this->DateCreat = $DateCreat;

        return $this;
    }

    /**
     * @return Collection<int, Ingredients>
     */
    public function getIngredients(): Collection
    {
        return $this->Ingredients;
    }

    public function addIngredient(Ingredients $ingredient): static
    {
        if (!$this->Ingredients->contains($ingredient)) {
            $this->Ingredients->add($ingredient);
            $ingredient->setUser($this);
        }

        return $this;
    }

    public function removeIngredient(Ingredients $ingredient): static
    {
        if ($this->Ingredients->removeElement($ingredient)) {
            // set the owning side to null (unless already changed)
            if ($ingredient->getUser() === $this) {
                $ingredient->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Recettes>
     */
    public function getRecettes(): Collection
    {
        return $this->recettes;
    }

    public function addRecette(Recettes $recette): static
    {
        if (!$this->recettes->contains($recette)) {
            $this->recettes->add($recette);
            $recette->setUser($this);
        }

        return $this;
    }

    public function removeRecette(Recettes $recette): static
    {
        if ($this->recettes->removeElement($recette)) {
            // set the owning side to null (unless already changed)
            if ($recette->getUser() === $this) {
                $recette->setUser(null);
            }
        }

        return $this;
    }
}
