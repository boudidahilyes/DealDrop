<?php

namespace App\Form;

use App\Entity\DeliveryMan;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;

class DeliveryManApplicationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('email', EmailType::class, [
                'label' => 'Email'
            ])
            ->add('userImage', FileType::class, [
                'label' => 'Picture',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Please select an image']),
                    new Image(['mimeTypes' => ["image/jpeg", "image/jpg", "image/png"]])
                ]
            ])
            ->add('driverLicenseFront', FileType::class, [
                'label' => 'Driver License Front Image',
                'constraints' => [
                    new NotBlank(['message' => 'Please select at least one image']),
                    new Image(['mimeTypes' => ["image/jpeg", "image/jpg", "image/png"]])
                ],
                'mapped' => false,
            ])
            ->add('driverLicenseBack', FileType::class, [
                'label' => 'Driver License Back Image',
                'constraints' => [
                    new NotBlank(['message' => 'Please select at least one image']),
                    new Image(['mimeTypes' => ["image/jpeg", "image/jpg", "image/png"]])
                ],
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DeliveryMan::class,
            'attr' => ['id' => 'applicationid'],
        ]);
    }
}
