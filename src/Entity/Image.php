<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;



#[ORM\Entity(repositoryClass: ImageRepository::class)]

#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap(['DriverLicenseImage' => DriverLicenseImage::class, 'ProductImage' => ProductImage::class, 'UserImage' => UserImage::class])]


class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    protected static ?string $PATH = 'public/uploads/driverLicense/';
    #[ORM\Column(length: 255)]
    protected ?string $title = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?\DateTimeInterface $modifyDate = null;

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

   

    public function getmodify(): ?\DateTimeInterface
    {
        return $this->modifyDate;
    }

    public function setmodify(?\DateTimeInterface $modifyDate): static
    {
        $this->modifyDate = $modifyDate;

        return $this;
    }
    
}
