<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class TestController extends AbstractController
{
    //todo chercher les utilisateurs dans la base et les afficher dans une reponse json pour tester les api
    #[Route('/api/test', methods:['GET'])]
    public function getUtilisateur(UserRepository $userRepository): JsonResponse
    {
        return new JsonResponse([
            "message" => "ceci est une api",
        ]);
    }
}
