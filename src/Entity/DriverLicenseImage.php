<?php

namespace App\Entity;

use App\Repository\DriverLicenseImageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DriverLicenseImageRepository::class)]
class DriverLicenseImage extends Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'driverLicenseImages')]
    private ?DeliveryMan $DeliveryMan = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDeliveryMan(): ?DeliveryMan
    {
        return $this->DeliveryMan;
    }

    public function setDeliveryMan(?DeliveryMan $DeliveryMan): static
    {
        $this->DeliveryMan = $DeliveryMan;

        return $this;
    }
}
