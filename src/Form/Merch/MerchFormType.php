<?php

namespace App\Form\Merch;

use App\Entity\Merch;
use App\Entity\Size;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class MerchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Název',
                'required' => true,
                'constraints' => [new NotBlank()]
            ])
            ->add('price', IntegerType::class, [
                'label' => 'Cena',
                'required' => true,
                'constraints' => [new NotBlank()]
            ])
            ->add('sizes', EntityType::class, [
                'label' => 'Velikosti',
                'class' => Size::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'choices' => $options['all_sizes'],
                'choice_attr' => function($choiceValue, $key, $value) use ($options) {
                    return in_array($value, $options['selected_sizes']) ? ['checked' => 'checked'] : [];
                },
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Popis',
                'required' => false,
                'attr' => [
                    'rows' => 3,
                ],
            ])
            ->add('images', FileType::class, [
                'label' => 'Obrázky',
                'required' => false,
                'mapped' => false,
                'multiple' => true,
                'constraints' => [
                    new All([
                        'constraints' => [
                            new File([
                                'maxSize' => '5M',
                                'mimeTypes' => [
                                    'image/*',
                                ],
                                'mimeTypesMessage' => 'Nahrajte obrázek (max size 5MB)!',
                            ])
                        ],
                    ]),
                ]
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
            'data_class' => Merch::class,
            'selected_sizes' => [],
            'all_sizes' => [],
        ]);
    }
}