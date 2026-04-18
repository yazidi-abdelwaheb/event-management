<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\EventSubscribe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventSubscribeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('full_name', TextType::class, [
            'label' => 'Full Name',
            'attr' => ['placeholder' => 'Your full name', 'class' => 'form-control']
        ])
        ->add('email', EmailType::class, [
            'label' => 'Email',
            'attr' => ['placeholder' => 'you@exemple.com', 'class' => 'form-control']
        ])
        ->add('phone', TelType::class, [
            'label' => 'Phone',
            'attr' => ['placeholder' => '8 digits', 'class' => 'form-control']
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventSubscribe::class,
        ]);
    }
}
