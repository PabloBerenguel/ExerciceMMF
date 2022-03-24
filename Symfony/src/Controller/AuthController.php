<?php

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthController extends AbstractController
{
    /**
     * @Route("/login", name="auth.login")
     */
    public function login() : JsonResponse
    {
        // ...
    }

    /**
     * @Route("/logout", name="auth.logout")
     */
    public function logout() : JsonResponse
    {
        // ...
    }
}
