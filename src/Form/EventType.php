<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Event;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Event Title',
                'attr'  => ['placeholder' => 'Enter event title'],
                'constraints' => [new NotBlank(message: 'Title is required')],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr'  => ['placeholder' => 'Describe your event...', 'rows' => 5],
                'constraints' => [new NotBlank(message: 'Description is required')],
            ])
            ->add('start_date_time', DateTimeType::class, [
                'label'  => 'Start Date & Time',
                'widget' => 'single_text',
            ])
            ->add('end_date_time', DateTimeType::class, [
                'label'    => 'End Date & Time',
                'widget'   => 'single_text',
                'required' => false,
            ])
            ->add('location', TextType::class, [
                'label' => 'Location',
                'attr'  => ['placeholder' => 'Event venue or address'],
            ])
            ->add('category', EntityType::class, [
                'label'        => 'Category',
                'class'        => Category::class,
                'choice_label' => 'label',
                'placeholder'  => 'Select a category',
            ])
            ->add('capacity', NumberType::class, [
                'label' => 'Capacity',
                'attr'  => ['placeholder' => 'Max number of participants'],
                'constraints' => [new Positive(message: 'Capacity must be positive')],
            ])
            ->add('price', MoneyType::class, [
                'label'    => 'Price',
                'currency' => false,
                'attr'     => ['placeholder' => '0.00'],
            ])
            ->add('imageFile', FileType::class, [
                'label'    => 'Event Image',
                'required' => false,
                'mapped'   => false,
                'constraints' => [
                    new File([
                        'maxSize'          => '2M',
                        'mimeTypes'        => ['image/jpeg', 'image/png', 'image/webp'],
                        'mimeTypesMessage' => 'Please upload a valid image (JPG, PNG, WEBP)',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}