<?php

namespace App\Form;

use App\Entity\Auction;
use App\Entity\Member;
use App\Entity\ProductCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class AuctionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('productCategory', EntityType::class, [
                'class' => ProductCategory::class,             
                  'choice_label' => 'name', 
            ])
            ->add('name',TextType::class)
            ->add('description',TextareaType::class)
            ->add('startDate',DateTimeType::class, ['widget' => 'single_text'])
            ->add('currentPrice',NumberType::class)
            ->add('endDate', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('productImage', FileType::class, [
                'mapped' => false,
                'multiple' => true,
            ])
            ->add('submit',SubmitType::class,['label'=>'Add Auction']);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Auction::class,
            

        ]);
    }
    
}
