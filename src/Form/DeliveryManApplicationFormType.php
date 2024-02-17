<?php

namespace App\Form;

use App\Entity\DeliveryMan;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ])
            ->add('driverLicense', FileType::class, [
                'label' => 'Driver License Images',
                'mapped' => false,
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DeliveryMan::class,
            'attr' => ['id' => 'applicationid'],
        ]);
    }
}
