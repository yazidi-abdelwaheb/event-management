<?php

namespace App\Form;

use App\Entity\EventSubscribe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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

        foreach ($options['customFields'] as $index => $customField) {
            $fieldName = sprintf('customField_%d', $index);
            $fieldConfig = $this->resolveFieldConfig($customField);

            $builder->add($fieldName, $fieldConfig['type'], array_merge([
                'label' => $customField['label'] ?? 'Additional question',
                'required' => (bool) ($customField['required'] ?? false),
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
            ], $fieldConfig['options']));
        }

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($options): void {
            $form = $event->getForm();
            $subscription = $event->getData();

            if (!$subscription instanceof EventSubscribe) {
                return;
            }

            $answers = [];
            foreach ($options['customFields'] as $index => $customField) {
                $fieldName = sprintf('customField_%d', $index);
                if (!$form->has($fieldName)) {
                    continue;
                }

                $answers[$customField['label'] ?? $fieldName] = $form->get($fieldName)->getData();
            }

            $subscription->setCustomAnswers($answers);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventSubscribe::class,
            'customFields' => [],
        ]);

        $resolver->setAllowedTypes('customFields', ['array']);
    }

    private function resolveFieldConfig(array $customField): array
    {
        $type = $customField['type'] ?? 'text';
        $options = [];

        if (in_array($type, ['select', 'checkboxes', 'radio'], true)) {
            $choices = $this->parseOptions($customField['options'] ?? '');

            if (empty($choices)) {
                return ['type' => TextType::class, 'options' => []];
            }

            $options = [
                'choices' => $choices,
                'choice_translation_domain' => false,
                'placeholder' => $type === 'select' ? 'Choose an option' : null,
            ];

            return match ($type) {
                'select' => ['type' => ChoiceType::class, 'options' => array_merge($options, ['expanded' => false, 'multiple' => false, 'attr' => ['class' => 'form-select']])],
                'checkboxes' => ['type' => ChoiceType::class, 'options' => array_merge($options, ['expanded' => true, 'multiple' => true, 'required' => (bool) ($customField['required'] ?? false)])],
                'radio' => ['type' => ChoiceType::class, 'options' => array_merge($options, ['expanded' => true, 'multiple' => false])],
                default => ['type' => TextType::class, 'options' => []],
            };
        }

        return match ($type) {
            'textarea' => ['type' => TextareaType::class, 'options' => ['attr' => ['class' => 'form-control']]],
            'email' => ['type' => EmailType::class, 'options' => ['attr' => ['class' => 'form-control']]],
            'tel' => ['type' => TelType::class, 'options' => ['attr' => ['class' => 'form-control']]],
            default => ['type' => TextType::class, 'options' => ['attr' => ['class' => 'form-control']]],
        };
    }

    private function parseOptions(string $options): array
    {
        $values = array_filter(array_map('trim', explode(',', $options)));

        return array_combine($values, $values) ?: [];
    }
}
