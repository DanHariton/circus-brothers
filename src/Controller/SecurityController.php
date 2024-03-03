<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Security\ChangePasswordFormType;
use App\Form\Security\LoginFormType;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    #[Route("/login", name: "security_login")]
	public function login(AuthenticationUtils $authenticationUtils): Response
	{
		if ($this->getUser()) {
			return $this->redirectToRoute('site_app_admin_redirect');
		}

		$form = $this->createForm(LoginFormType::class, [
			'defaultUsername' => $authenticationUtils->getLastUsername()
		]);

        $error = $authenticationUtils->getLastAuthenticationError();
		if ($error !== null) {
			$this->addFlash('danger', $error->getMessageKey());
		}

		return $this->render('/admin/security/login.html.twig', [
			'loginForm' => $form->createView()
		]);
	}

    #[Route("/logout", name: "security_logout")]
	public function logout(): void
	{
		throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
	}

    #[Route("/csp-report")]
	public function cspReport(): Response
	{
		return new Response();
	}

    #[Route("/admin/users/change-password/{user}", name: "security_change_password")]
    public function changePassword(User $user, Request $request, UserPasswordHasherInterface $passwordHasher): RedirectResponse|Response
    {
        $form = $this->createForm(ChangePasswordFormType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordHasher->hashPassword($user, $form->get('password')->getData()));

            $this->em->persist($user);
            $this->em->flush();

            return $this->redirectToRoute('user_edit', ['user' => $user->getId()]);
        }

        return $this->render('admin/security/change_password.html.twig', [
           'form' => $form->createView(),
           'user' => $user
        ]);
    }
}