<?php

namespace App\Interfaces;

interface AuthenticationInterface
{
    public function login(array $credentials);
    public function register(array $data);
    public function logout();
    public function refreshToken();
}