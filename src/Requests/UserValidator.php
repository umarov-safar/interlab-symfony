<?php

namespace App\Requests;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserValidator extends BaseValidator
{
    #[NotBlank]
    protected string $name;

    #[NotBlank]
    #[Email]
    protected string $email;

    #[NotBlank]
    protected string $password;
}