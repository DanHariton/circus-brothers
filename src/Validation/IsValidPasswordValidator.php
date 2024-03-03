<?php

namespace App\Validation;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;

class IsValidPasswordValidator extends ConstraintValidator
{
    /** @var TokenInterface|null $token */
    private ?TokenInterface $token;

    public function __construct(TokenStorageInterface $tokenStorage, private readonly UserPasswordHasherInterface $passwordEncoder,
                                private readonly TranslatorInterface $translator)
    {
        $this->token = $tokenStorage->getToken();
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof IsValidPassword) {
            throw new UnexpectedTypeException($constraint, IsValidPassword::class);
        }

        if (!$this->token) {
            return;
        }

        $user = $this->token->getUser();

        if (!($user instanceof User)) {
            return;
        }

        if ($this->passwordEncoder->isPasswordValid($user, strval($value))) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ message }}', $this->translator->trans('form_change_password.password.invalid_password', [], 'form_change_password'))
            ->addViolation();
    }
}