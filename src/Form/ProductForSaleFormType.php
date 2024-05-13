<?php

namespace App\Form;

use App\Entity\ProductCategory;
use App\Entity\ProductForSale;
use Doctrine\DBAL\Types\FloatType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductForSaleFormType extends AbstractType
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('productCategory', EntityType::class, [
                'class'  => ProductCategory::class,
                'choice_label' => 'name', 
            ])
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('price', NumberType::class)
            ->add('productImage', FileType::class, [
                'mapped' => false,
                'multiple' => true,
            ])
            ->add('submit',SubmitType::class,['label'=>'Add Product']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductForSale::class,
        ]);
    }
}
