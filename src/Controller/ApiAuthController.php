<?php

namespace App\Controller;

use App\Entity\UTILISATEURS;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UTILISATEURSRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class ApiAuthController extends AbstractController
{
    #[Route('/api/login', name: 'app_api_auth_login',methods: ['POST'])]
    public function login(Request $request, UTILISATEURSRepository $UTILISATEURSRepository): Response
    {
        $data = json_decode($request -> getContent(), true);
        $email = $data['email'];
        $password = $data['password'];
        $user = $UTILISATEURSRepository->findOneBY(['EMAIL' => $email]);

        if (!$user || $user->getPASSWORD() != $password ){
            return new JsonResponse(['message' => "Email ou mot de passe invalide veuillez re essayer,merci"]);
        }

        $request -> getSession()->set('user_id', $user->getId());
        return new JsonResponse(['message' => 'Connexion réussie']);
    }

    #[Route('/api/register', name: 'app_api_auth_register',methods: ['POST'])]
    public function register(Request $request,EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher){
        $data = json_decode($request -> getContent(),true);

        $pseudo = $data['pseudo'];
        $email = $data['email'];
        $age = $data['age'];
        $password = $data['password'];

        $user = new UTILISATEURS();
        $user->setPseudo($pseudo);
        $user->setEMAIL($email);
        $user->setAGE($age);
        $user->setSUBSCRIBERS(0);
        $user->setUPLOADEDVIDEO(0);
        $user->setISADMIN(false);
        $user->setJOINDATE(new \DateTime());
        $hashedpassword = $passwordHasher;



    }




}
