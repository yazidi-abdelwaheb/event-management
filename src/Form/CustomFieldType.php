<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', TextType::class, [
                'label' => 'Field label',
                'required' => true,
                'attr' => ['placeholder' => 'e.g. Company name', 'class' => 'form-control'],
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Field type',
                'choices' => [
                    'Text' => 'text',
                    'Textarea' => 'textarea',
                    'Email' => 'email',
                    'Phone' => 'tel',
                    'Select' => 'select',
                    'Checkboxes' => 'checkboxes',
                    'Radio buttons' => 'radio',
                ],
                'required' => true,
                'attr' => ['class' => 'form-select custom-field-type'],
            ])
            ->add('options', TextType::class, [
                'label' => 'Options (comma-separated)',
                'required' => false,
                'attr' => ['placeholder' => 'Option 1, Option 2, Option 3', 'class' => 'form-control custom-field-options'],
            ])
            ->add('required', CheckboxType::class, [
                'label' => 'Required',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }

}
