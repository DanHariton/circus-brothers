<?php

namespace App\Validation;

use Symfony\Component\Validator\Constraint;

class IsValidPassword extends Constraint
{
    /** @var string $message */
    public string $message = '{{ message }}';
}