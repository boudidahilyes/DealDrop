<?php

namespace App\Entity;

use App\Repository\ProductForSaleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: ProductForSaleRepository::class)]
class ProductForSale extends Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'The price is required')]
    #[Assert\Positive(message:'The price should be positive')]
    private ?float $price = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }
    public function whoIAm()
    {
        return 'ProductForSale';
    }
}
