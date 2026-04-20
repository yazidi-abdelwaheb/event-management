<?php
// src/Form/CommentType.php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('body', TextareaType::class, [
            'label' => false,
            'attr'  => [
                'placeholder' => 'Write your comment...',
                'rows'        => 4,
                'class'       => 'form-control',
            ],
            'constraints' => [
                new NotBlank(message: 'Comment cannot be empty.'),
                new Length(min: 3, minMessage: 'Comment must be at least 3 characters.'),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Comment::class]);
    }
}