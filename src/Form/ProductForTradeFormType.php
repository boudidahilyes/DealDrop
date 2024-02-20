<?php

namespace App\Form;

use App\Entity\ProductForTrade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductForTradeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('approved')
            ->add('addDate')
            ->add('tradeType')
            ->add('productCategory')
            ->add('owner')
            ->add('chosenOffer')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductForTrade::class,
        ]);
    }
}
