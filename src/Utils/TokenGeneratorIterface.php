<?php

namespace App\Utils;

interface TokenGeneratorIterface
{
    public function generateToken(): string;
}