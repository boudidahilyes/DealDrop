<?php

namespace App\Entity;

use App\Repository\OfferRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OfferRepository::class)]
class Offer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'offers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ProductForTrade $productPosted = null;

    #[ORM\OneToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?ProductForTrade $productOffered = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductPosted(): ?ProductForTrade
    {
        return $this->productPosted;
    }

    public function setProductPosted(?ProductForTrade $productPosted): static
    {
        $this->productPosted = $productPosted;

        return $this;
    }

    public function getProductOffered(): ?ProductForTrade
    {
        return $this->productOffered;
    }

    public function setProductOffered(ProductForTrade $productOffered): static
    {
        $this->productOffered = $productOffered;

        return $this;
    }

}
