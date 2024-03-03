<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 *
 */
class Authenticator extends AbstractAuthenticator
{
	use TargetPathTrait;

	/** @var EntityManagerInterface */
	private EntityManagerInterface $entityManager;

	/** @var UrlGeneratorInterface */
	private UrlGeneratorInterface $urlGenerator;

    /**
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     */
	public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator)
	{
		$this->entityManager = $entityManager;
		$this->urlGenerator = $urlGenerator;
	}

    /**
     * @param Request $request
     * @return bool|null
     */
	public function supports(Request $request): ?bool
	{
		return $request->attributes->get('_route') === 'security_login' && $request->isMethod('POST');
	}

    /**
     * @param Request $request
     * @return Passport
     */
	public function authenticate(Request $request): Passport
	{
		$formData = $request->get('login_form');
		if (!is_array($formData) ||
			!isset($formData['email']) ||
			!isset($formData['password']) ||
			!isset($formData['csrfToken'])) {
			throw new InvalidArgumentException('Invalid credentials data', Response::HTTP_BAD_REQUEST);
		}

		$credentials = [
			'email'		=> $formData['email'],
			'password'	=> $formData['password'],
			'csrfToken'	=> $formData['csrfToken']
		];

		$request->getSession()->set(Security::LAST_USERNAME, $credentials['email']);

		return new Passport(
			new UserBadge($credentials['email'], function(string $userIdentifier) {
				return $this->entityManager->getRepository(User::class)->findOneBy([
					'email' => $userIdentifier
				]);
			}),
			new PasswordCredentials($credentials['password']), [
				new CsrfTokenBadge('authenticate', $credentials['csrfToken']),
				new RememberMeBadge()
			]
		);
	}

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $firewallName
     * @return Response|null
     */
	public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
	{
		if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
			return new RedirectResponse($targetPath);
		}

		return new RedirectResponse($this->urlGenerator->generate('site_app_admin_redirect'));
	}

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return Response|null
     */
	public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
	{
        if ($exception->getMessage()) {
            if ($exception->getPrevious()) {
                $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception->getPrevious());
            }
			$request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
		}

		return new RedirectResponse($this->urlGenerator->generate('security_login'));
	}
}