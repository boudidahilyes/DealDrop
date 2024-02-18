<?php

namespace App\Entity;

use App\Repository\ProductForRentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductForRentRepository::class)]
class ProductForRent extends Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column]
    private ?float $pricePerDay = null;

    #[ORM\Column(length: 255)]
    private ?string $disponibility = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPricePerDay(): ?float
    {
        return $this->pricePerDay;
    }

    public function setPricePerDay(float $pricePerDay): static
    {
        $this->pricePerDay = $pricePerDay;

        return $this;
    }


    public function getDisponibility(): ?string
    {
        return $this->disponibility;
    }

    public function setDisponibility(string $disponibility): static
    {
        $this->disponibility = $disponibility;

        return $this;
    }
    public function whoIAm()
    {
        return 'ProductForRent';
    }
}
