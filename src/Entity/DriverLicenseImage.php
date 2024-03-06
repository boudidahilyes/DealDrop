<?php

namespace App\Entity;

use App\Repository\DriverLicenseImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: DriverLicenseImageRepository::class)]
#[Vich\Uploadable]
class DriverLicenseImage extends Image
{

    #[ORM\ManyToOne(inversedBy: 'driverLicenseImages')]
    #[ORM\JoinColumn(nullable: true)]
    private ?DeliveryMan $DeliveryMan = null;

    // NOTE: This is not a mapped field of entity metadata, just a simple property.
    #[Vich\UploadableField(mapping: 'driverLicense', fileNameProperty: 'title')]
    protected ?File $imageFile = null;

    public function getDeliveryMan(): ?DeliveryMan
    {
        return $this->DeliveryMan;
    }

    public function setDeliveryMan(?DeliveryMan $DeliveryMan): static
    {
        $this->DeliveryMan = $DeliveryMan;

        return $this;
    }
    /**
     * Get the value of imageFile
     */ 
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * Set the value of imageFile
     *
     * @return  self
     */ 
    public function setImageFile($imageFile)
    {
        $this->imageFile = $imageFile;
        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->modifyDate = new \DateTimeImmutable();
        }
        return $this;
    }
}
