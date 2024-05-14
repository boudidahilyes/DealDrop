<?php

namespace App\Form;

use App\Entity\ProductCategory;
use App\Entity\ProductForTrade;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductForTradeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('productCategory', EntityType::class, [
                'class'  => ProductCategory::class,
                'choice_label' => 'name',
            ])
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('productImage', FileType::class, [
                'multiple' => true,
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Please select at least one image']),
                    new All([
                        'constraints' => [
                            new Image([
                                'mimeTypes' => ["image/jpeg", "image/jpg", "image/png"],
                                'mimeTypesMessage' => 'Please upload only JPEG, JPG, or PNG images.'
                            ])
                        ]
                    ])
                ]
            ])
            ->add('submit', SubmitType::class, ['label' => 'Add Product']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductForTrade::class,
        ]);
    }
}
