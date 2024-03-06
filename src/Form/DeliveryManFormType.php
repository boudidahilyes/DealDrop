<?php

namespace App\Form;

use App\Entity\DeliveryMan;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeliveryManFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('cin')
            ->add('password')
            ->add('adress')
            ->add('phone')
            ->add('disponibility')
            ->add('status')
            ->add('area')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DeliveryMan::class,
        ]);
    }
}
