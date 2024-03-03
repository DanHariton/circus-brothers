<?php

namespace App\Form\Security;

use App\Validation\IsValidPassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChangePasswordFormType extends AbstractType
{

    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('passwordBefore', PasswordType::class, [
                'mapped' => false,
                'constraints' => [new IsValidPassword()],
            ])
            ->add('password', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'invalid_message' => $this->translator->trans('change_password_form.password.invalid_message', [], 'form_change_password'),
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'constraints' => [new Length(['min' => 6])],
            ])
            ->add('submit', SubmitType::class, [
                'translation_domain' => false,
                'label_html' =>  true,
                'label' => '<i class="far fa-save me-2"></i> ' . $this->translator->trans('change_password_form.button', [], 'form_change_password'),
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'form_change_password',
        ]);
    }
}