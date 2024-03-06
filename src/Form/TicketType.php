<?php

namespace App\Form;

use App\Entity\SupportTicket;
use App\Entity\SupportTicketCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('subject')
        ->add('description')
        ->add('supportTicketCategory', EntityType::class,[
            'class' => SupportTicketCategory::class,
            'choice_label' => 'name',
        ])
        ->add('Submit',SubmitType::class);
    
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SupportTicket::class,
        ]);
    }
}
