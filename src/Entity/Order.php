<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'The adress is required')]
    #[Assert\Length(
        min:5,
        max:150,
        minMessage : "Your Adress is too short",
        maxMessage : "Your Adress cannot be longer than {{ limit }} characters")]
    private ?string $deliveryAdress = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $orderDate = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Member $member = null;

    #[Assert\NotBlank(message:'The rent days is required')]
    #[Assert\Positive(message:'The rent days should be positive')]
    #[ORM\Column(nullable: true)]
    private ?int $rentDays = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $payment = null;

    #[ORM\ManyToOne(inversedBy: 'orders',cascade: ['persist', 'remove'])]
    private ?Product $products = null;

    #[ORM\Column(nullable: true)]
    private ?float $priceAdded = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDeliveryAdress(): ?string
    {
        return $this->deliveryAdress;
    }

    public function setDeliveryAdress(string $deliveryAdress): static
    {
        $this->deliveryAdress = $deliveryAdress;

        return $this;
    }

    public function getOrderDate(): ?\DateTimeInterface
    {
        return $this->orderDate;
    }

    public function setOrderDate(\DateTimeInterface $orderDate): static
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    public function getMember(): ?Member
    {
        return $this->member;
    }

    public function setMember(?Member $member): static
    {
        $this->member = $member;

        return $this;
    }


    public function getRentDays(): ?int
    {
        return $this->rentDays;
    }

    public function setRentDays(?int $rentDays): static
    {
        $this->rentDays = $rentDays;

        return $this;
    }

    public function getPayment(): ?string
    {
        return $this->payment;
    }

    public function setPayment(?string $payment): static
    {
        $this->payment = $payment;

        return $this;
    }

    public function getProducts(): ?product
    {
        return $this->products;
    }

    public function setProducts(?product $products): static
    {
        $this->products = $products;

        return $this;
    }

    public function getPriceAdded(): ?float
    {
        return $this->priceAdded;
    }

    public function setPriceAdded(?float $priceAdded): static
    {
        $this->priceAdded = $priceAdded;

        return $this;
    }
}
