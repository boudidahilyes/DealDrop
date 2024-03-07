<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email as ConstraintsEmail;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\Regex;

class EditUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Email cannot be blank']),
                    new ConstraintsEmail(['message' => 'Email is not valid']),
                ],
            ])
            //->add('password')
            ->add('firstName',TextType::class,[
                'constraints' => [
                    new NotBlank(['message' => 'This field cannot be blank']),

                ],
            ])
            ->add('lastName',TextType::class,[
                'constraints' => [
                    new NotBlank(['message' => 'This field cannot be blank']),

                ],
            ])
            ->add('cin')
            ->add('phone', NumberType::class, [
                'constraints' =>
                [
                    new NotBlank(
                        ['message' =>
                        'Phone Number cannot be blank']
                    ),
                    new Regex([
                        'pattern' => '/^\+?[0-9]{8,}$/',
                        'message' => 'Please enter a valid phone number'
                    ])

                ]
            ])

            ->add('address', CountryType::class, [
                'placeholder' => 'Choose a country',
                'preferred_choices' => ['Tunisie'],

            ])
            /*->add('birthDate', DateType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a valid birthday.',
                    ]),
                    new LessThan([
                        'value' => 'today',
                        'message' => 'The birthday cannot be in the future.',
                    ]),
                ],
            ])

            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options' => [
                    'label' => 'Your Password',
                    'attr' => ['autocomplete' => 'new-password'],
                ],
                'second_options' => [
                    'label' => 'Repeat Password',
                    'attr' => ['autocomplete' => 'new-password'],
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'max' => 4096,
                    ]),
                ],
            ])*/




            ->add('submit', SubmitType::class, [
                'attr' => [
                    'value' => 'edit',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
