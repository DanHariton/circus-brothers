<?php

namespace App\Form\Concert;

use App\Entity\Concert;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ConcertFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Název',
                'required' => true,
                'constraints' => [new NotBlank()]
            ])
            ->add('date', DateType::class, [
                'label' => 'Datum',
                'required' => true,
                'widget' => 'single_text',
                'constraints' => [new NotBlank()]
            ])
            ->add('location', TextType::class, [
                'label' => 'Místo konání',
                'required' => true,
                'constraints' => [new NotBlank()]
            ])
            ->add('tickets', TextType::class, [
                'label' => 'Vstupenky',
                'required' => true,
                'constraints' => [new NotBlank()]
            ])
            ->add('active', CheckboxType::class, [
                'label' => 'Aktivní',
                'attr' => [
                    'checked' => 'checked'
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Uložit',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Concert::class,
        ]);
    }
}