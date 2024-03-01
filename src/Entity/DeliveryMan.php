<?php

namespace App\Entity;

use App\Repository\DeliveryManRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Validator\Constraints as MyConstraints;
#[ORM\Entity(repositoryClass: DeliveryManRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class DeliveryMan extends User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $disponibility = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $location = null;

    #[MyConstraints\VerticesConstraint()]
     //#[MyConstraints\AreaConstraint()]
    #[ORM\Column(type: Types::TEXT, nullable:true)]
    private ?string $area = null;

    #[ORM\OneToMany(mappedBy: 'DeliveryMan', targetEntity: DriverLicenseImage::class, cascade: ['persist', 'remove'])]
    private Collection $driverLicenseImages;

    #[ORM\OneToMany(mappedBy: 'deliveryMan', targetEntity: Delivery::class)]
    private Collection $deliveries;

    public function __construct()
    {
        $this->firstName = "";
        $this->lastName = "";
        $this->cin = 0;
        $this->password = "";
        $this->adress = "";
        $this->phone = 0;
        $this->firstName = "";
        $this->disponibility = "";
        $this->status = "Under Review";
        $this->location = "";
        $this->area = "";
        parent::__construct();
        $this->driverLicenseImages = new ArrayCollection();
        $this->deliveries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getArea(): ?string
    {
        return $this->area;
    }

    public function setArea(?string $area): static
    {
        $this->area = $area;

        return $this;
    }

    /**
     * @return Collection<int, DriverLicenseImage>
     */
    public function getDriverLicenseImages(): Collection
    {
        return $this->driverLicenseImages;
    }

    public function addDriverLicenseImage(DriverLicenseImage $driverLicenseImage): static
    {
        if (!$this->driverLicenseImages->contains($driverLicenseImage)) {
            $this->driverLicenseImages->add($driverLicenseImage);
            $driverLicenseImage->setDeliveryMan($this);
        }

        return $this;
    }

    public function removeDriverLicenseImage(DriverLicenseImage $driverLicenseImage): static
    {
        if ($this->driverLicenseImages->removeElement($driverLicenseImage)) {
            // set the owning side to null (unless already changed)
            if ($driverLicenseImage->getDeliveryMan() === $this) {
                $driverLicenseImage->setDeliveryMan(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Delivery>
     */
    public function getDeliveries(): Collection
    {
        return $this->deliveries;
    }

    public function addDelivery(Delivery $delivery): static
    {
        if (!$this->deliveries->contains($delivery)) {
            $this->deliveries->add($delivery);
            $delivery->setDeliveryMan($this);
        }

        return $this;
    }

    public function removeDelivery(Delivery $delivery): static
    {
        if ($this->deliveries->removeElement($delivery)) {
            // set the owning side to null (unless already changed)
            if ($delivery->getDeliveryMan() === $this) {
                $delivery->setDeliveryMan(null);
            }
        }

        return $this;
    }
}
