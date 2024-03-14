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
                'required' => true,
                'constraints' => [new NotBlank()]
            ])
            ->add('price', IntegerType::class, [
                'required' => true,
                'constraints' => [new NotBlank()]
            ])
            ->add('sizes', EntityType::class, [
                'class' => Size::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'choices' => $options['all_sizes'],
                'choice_attr' => function($choiceValue, $key, $value) use ($options) {
                    return in_array($value, $options['selected_sizes']) ? ['checked' => 'checked'] : [];
                },
            ])
            ->add('images', FileType::class, [
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
                'attr' => [
                    'checked' => 'checked'
                ],
            ])
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Merch::class,
            'translation_domain' => 'form_edit_merch',
            'selected_sizes' => [],
            'all_sizes' => [],
        ]);
    }
}