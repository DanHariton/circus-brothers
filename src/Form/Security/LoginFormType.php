<?php

namespace App\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 *
 */
class LoginFormType extends AbstractType
{

	/** @var CsrfTokenManagerInterface */
	private CsrfTokenManagerInterface $csrfTokenManager;

	/**
	 *
	 */
	public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
	{
		$this->csrfTokenManager = $csrfTokenManager;
	}

	/**
	 *
	 */
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$defaultUserName = is_array($options['data']) && isset($options['data']['defaultUsername'])
			? $options['data']['defaultUsername']
			: null;

		$builder
			->add('email', TextType::class, [
				'data' => $defaultUserName
			])
			->add('password', PasswordType::class)
			->add('csrfToken', HiddenType::class, [
				'data' => $this->csrfTokenManager->getToken('authenticate')->getValue()
			])
			->add('submit', SubmitType::class);
    }

	/**
	 *
	 */
	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefault('translation_domain', 'form_login');
	}
}