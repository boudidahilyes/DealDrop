<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Mime\Message;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap(['DeliveryMan' => DeliveryMan::class, 'Member' => Member::class, 'Admin' => Admin::class])]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;
    
    #[Assert\Email()]
    #[ORM\Column(length: 180, unique: true)]
    protected ?string $email = null;

    #[ORM\Column]
    protected array $roles = [];

    /**
     * @var string The hashed password
     */
    #[Assert\Regex(
        pattern:'/[a-zA-Z]*[0-9][a-zA-Z]*/',
        message:'Password should containt a number'
        )]
    #[ORM\Column]
    protected ?string $password = null;

    #[Assert\Length(
        min: 3,
        max: 30,
        minMessage: 'Your Firstname must be at least {{ limit }} characters long',
        maxMessage: 'Your Firstname cannot be longer than {{ limit }} characters',
    )]
    #[Assert\NotBlank()]
    #[ORM\Column(length: 255)]
    protected ?string $firstName = null;

    #[Assert\NotBlank()]
    #[Assert\Length(
        min: 3,
        max: 30,
        minMessage: 'Your Lastname must be at least {{ limit }} characters long',
        maxMessage: 'Your Lastname cannot be longer than {{ limit }} characters',
    )]
    #[ORM\Column(length: 255)]
    protected ?string $lastName = null;

    #[Assert\Length(
        min: 8,
        max: 8,
        minMessage: 'Your CIN must be {{ limit }} characters long',
        maxMessage: 'Your CIN must be {{ limit }} characters',
    )]
    #[ORM\Column]
    protected ?int $cin = null;

    #[Assert\Length(
        min: 50,
        max: 255,
        minMessage: 'Your Adress must be at least {{ limit }} characters long',
        maxMessage: 'Your Adress cannot be longer than {{ limit }} characters',
    )]
    #[ORM\Column(length: 255)]
    protected ?string $adress = null;

    #[Assert\Length(
        min: 8,
        max: 8,
        minMessage: 'Your Phone number must be {{ limit }} characters long',
        maxMessage: 'Your Phone number must be {{ limit }} characters',
    )]
    #[ORM\Column]
    protected ?int $phone = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: SupportTicket::class, orphanRemoval: true)]
    private Collection $supportTickets;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?UserImage $userImage = null;

    public function __construct()
    {
        $this->supportTickets = new ArrayCollection();
    }



    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getCin(): ?int
    {
        return $this->cin;
    }

    public function setCin(int $cin): static
    {
        $this->cin = $cin;

        return $this;
    }

   

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(?string $adress): static
    {
        $this->adress = $adress;

        return $this;
    }

    public function getPhone(): ?int
    {
        return $this->phone;
    }

    public function setPhone(int $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Collection<int, SupportTicket>
     */
    public function getSupportTickets(): Collection
    {
        return $this->supportTickets;
    }

    public function addSupportTicket(SupportTicket $supportTicket): static
    {
        if (!$this->supportTickets->contains($supportTicket)) {
            $this->supportTickets->add($supportTicket);
            $supportTicket->setUser($this);
        }

        return $this;
    }

    public function removeSupportTicket(SupportTicket $supportTicket): static
    {
        if ($this->supportTickets->removeElement($supportTicket)) {
            // set the owning side to null (unless already changed)
            if ($supportTicket->getUser() === $this) {
                $supportTicket->setUser(null);
            }
        }

        return $this;
    }

    public function getUserImage(): ?UserImage
    {
        return $this->userImage;
    }

    public function setUserImage(UserImage $userImage): static
    {
        // set the owning side of the relation if necessary
        if ($userImage->getUser() !== $this) {
            $userImage->setUser($this);
        }

        $this->userImage = $userImage;

        return $this;
    }




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
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
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
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

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
