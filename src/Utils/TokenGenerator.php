<?php

namespace App\Utils;

final class TokenGenerator implements TokenGeneratorIterface
{
    public function __invoke(): string
    {
        return $this->generateToken();
    }

    public function generateToken(): string
    {
        return bin2hex(random_bytes(60));
    }
}