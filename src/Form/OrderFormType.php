<?php

namespace App\Form;

use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('deliveryAdress',TextType::class , [
                'attr' => [
                    'id' => 'adress'
                ]
            ])
            ->add('coordinates', TextType::class ,[
                'mapped' => false,
                'attr' => [
                    'hidden' => true,
                ]
            ])
            ->add('submit',SubmitType::class,['label'=>'Place
            Order Now!']);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
