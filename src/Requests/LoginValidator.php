<?php

namespace App\Requests;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class LoginValidator extends BaseValidator
{
    #[NotBlank]
    #[Email]
    protected string $email;

    #[NotBlank]
    protected string $password;
}