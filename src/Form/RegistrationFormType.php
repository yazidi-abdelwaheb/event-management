<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Constraints\PasswordStrength;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
            'attr' => [
                'class'=> 'form-control',
                'placeholder' => 'First Name'
            ],
        ])
        ->add('lastName', TextType::class, [
            'attr' => [
                'class'=> 'form-control',
                'placeholder' => 'Last Name'
            ],
        ])
        ->add('email', EmailType::class, [
            'attr' => [
                'class'=> 'form-control',
                'placeholder' => 'Email address'
            ],
        ])
        ->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'mapped' => false,
            'first_options'  => [
                'constraints' => [
                        new NotBlank(
                            message: 'Please enter a password',
                        ),
                        new Length(
                            min: 6,
                            minMessage: 'Your password should be at least {{ limit }} characters',
                            // max length allowed by Symfony for security reasons
                            max: 4096,
                        ),
                        
                    ],
                'label' => 'Password',
                'attr' => [
                    'class'=> 'form-control',
                    'placeholder' => 'Enter password'
                ]
            ],
            'second_options' => [
                'label' => 'Confirm Password',
                'attr' => [
                    'class'=> 'form-control',
                    'placeholder' => 'Confirm password'
                ]
            ],
            'invalid_message' => 'Passwords must match.',
            ],
        )   
        
        ->add('agreeTerms', CheckboxType::class, [
            'mapped' => false,
            'constraints' => [
                new IsTrue([
                    'message' => 'You must accept terms.',
                ]),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
