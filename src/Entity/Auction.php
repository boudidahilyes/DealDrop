<?php

namespace App\Entity;

use App\Repository\AuctionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PhpParser\Node\NullableType;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: AuctionRepository::class)]
class Auction extends Product{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;


    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: 'This value should not be blank')]
    #[Assert\GreaterThan(value: "today", message: "The start date must be after the current date.")]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'This value should not be blank')]
    private ?float $currentPrice = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: 'This value should not be blank')]
    #[Assert\GreaterThan(propertyPath: "startDate", message: "The end date must be after than the start date.")]

    private ?\DateTimeInterface $endDate = null;

    #[ORM\OneToMany(mappedBy: 'auction', targetEntity: Bid::class, orphanRemoval: true)]
    private Collection $bids;


    

    #[ORM\OneToOne(mappedBy: 'auction', cascade: ['persist', 'remove'])]
    private ?Reminder $reminder = null;

    public function __construct()
    { 
        $this->bids = new ArrayCollection();
       
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getCurrentPrice(): ?float
    {
        return $this->currentPrice;
    }

    public function setCurrentPrice(float $currentPrice): static
    {
        $this->currentPrice = $currentPrice;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }
    

    /**
     * @return Collection<int, Bid>
     */
    public function getBids(): Collection
    {
        return $this->bids;
    }

    public function addBid(Bid $bid): static
    {
        if (!$this->bids->contains($bid)) {
            $this->bids->add($bid);
            $bid->setAuction($this);
        }

        return $this;
    }

    public function removeBid(Bid $bid): static
    {
        if ($this->bids->removeElement($bid)) {
            // set the owning side to null (unless already changed)
            if ($bid->getAuction() === $this) {
                $bid->setAuction(null);
            }
        }

        return $this;
    }


    public function getReminder(): ?Reminder
    {
        return $this->reminder;
    }

    public function setReminder(Reminder $reminder): static
    {
        // set the owning side of the relation if necessary
        if ($reminder->getAuction() !== $this) {
            $reminder->setAuction($this);
        }

        $this->reminder = $reminder;

        return $this;
    }

    
}
