<?php

namespace App\Form\MediaContent;

use App\Entity\MediaContent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class MediaContentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'constraints' => [new NotBlank()]
            ]);

        if ($options['photo_content'] == MediaContent::PHOTO || !$options['photo_content']) {
            $builder
                ->add('image', FileType::class, [
                    'required' => false,
                    'mapped' => false,
                    'constraints' => [
                        new File([
                            'maxSize' => '5000k',
                            'mimeTypes' => [
                                'image/*',
                            ],
                            'mimeTypesMessage' => 'Nahrajte obrázek (max size 5MB)!',
                        ])
                    ],
                ])
                ->add('fullWidth', CheckboxType::class, [
                    'required' => false,
                ]);
        }

        if ($options['photo_content'] == MediaContent::VIDEO || !$options['photo_content']) {
            $builder
                ->add('videoLink', TextType::class, [
                    'required' => false
                ]);
        }
        $builder
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
            'data_class' => MediaContent::class,
            'translation_domain' => 'form_media_content',
            'photo_content' => null
        ]);
    }
}