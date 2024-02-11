<?php

namespace App\Entity;

use App\Repository\ProductForTradeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductForTradeRepository::class)]
class ProductForTrade extends Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $tradeType = null;

    #[ORM\OneToMany(mappedBy: 'productPosted', targetEntity: Offer::class, orphanRemoval: true)]
    private Collection $offers;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Offer $chosenOffer = null;
    
    public function __construct()
    {
        parent::__construct();
        $this->offers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTradeType(): ?string
    {
        return $this->tradeType;
    }

    public function setTradeType(string $tradeType): static
    {
        $this->tradeType = $tradeType;

        return $this;
    }

    /**
     * @return Collection<int, Offer>
     */
    public function getOffers(): Collection
    {
        return $this->offers;
    }

    public function addOffer(Offer $offer): static
    {
        if (!$this->offers->contains($offer)) {
            $this->offers->add($offer);
            $offer->setProductPosted($this);
        }

        return $this;
    }

    public function removeOffer(Offer $offer): static
    {
        if ($this->offers->removeElement($offer)) {
            // set the owning side to null (unless already changed)
            if ($offer->getProductPosted() === $this) {
                $offer->setProductPosted(null);
            }
        }

        return $this;
    }

    public function getChosenOffer(): ?offer
    {
        return $this->chosenOffer;
    }

    public function setChosenOffer(?offer $chosenOffer): static
    {
        $this->chosenOffer = $chosenOffer;

        return $this;
    }
}
