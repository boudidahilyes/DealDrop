<?php

namespace App\Entity;

use App\Repository\BidRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Validator\Constraints as MyConstraints;
use Proxies\__CG__\App\Entity\Auction;

#[ORM\Entity(repositoryClass: BidRepository::class)]
class Bid
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable:true)]
    private ?\DateTimeInterface $bidDate = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'This value should not be blank')]

    #[MyConstraints\BidValueConstraint("auction.highestBid.value")]

    private ?float $value = null;

    #[ORM\Column(length: 255, nullable:true)]
    private ?string $state = null;

    #[ORM\ManyToOne(inversedBy: 'bids')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Member $bidder = null;

    #[ORM\ManyToOne(inversedBy: 'bids')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Auction $auction = null;

    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBidDate(): ?\DateTimeInterface
    {
        return $this->bidDate;
    }

    public function setBidDate(\DateTimeInterface $bidDate): static
    {
        $this->bidDate = $bidDate;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getBidder(): ?Member
    {
        return $this->bidder;
    }

    public function setBidder(?Member $bidder): static
    {
        $this->bidder = $bidder;

        return $this;
    }

    public function getAuction(): ?Auction
    {
        return $this->auction;
    }

    public function setAuction(?Auction $auction): static
    {
        $this->auction = $auction;

        return $this;
    }
}
