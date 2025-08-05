<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
//use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Annotation\Route;

class LoginController
{
    #[Route('/api/admin/login', name: 'api_admin_login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        // This code should never run — handled by json_login firewall
        return new JsonResponse(['error' => 'This should be handled by the firewall'], 401);
    }
}
