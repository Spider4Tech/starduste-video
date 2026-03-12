<?php

namespace App\Controller;

use App\Entity\Utilisateurs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UtilisateursRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class ApiAuthController extends AbstractController
{
    #[Route('/api/login', name: 'app_api_auth_login',methods: ['POST'])]
    public function login(Request $request, UtilisateursRepository $UTILISATEURSRepository, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $data = json_decode($request -> getContent(), true);
        $email = $data['email'];
        $password = $data['password'];
        $user = $UTILISATEURSRepository->findOneBy(['EMAIL' => $email]);

        $csrfToken = new CsrfToken('login', $data['token']);

        if (!$csrfTokenManager->isTokenValid($csrfToken)) {
            return new JsonResponse(['error' => 'Invalid CSRF token'], 403);
        }

        if (!$user || $passwordHasher->isPasswordValid($user, $password)){
            return new JsonResponse(['message' => "Email ou mot de passe invalide veuillez re essayer,merci"]);
        }

        $request -> getSession()->set('user_id', $user->getId());
        return new JsonResponse(['message' => 'Connexion réussie']);
    }

    #[Route('/api/register', name: 'app_api_auth_register',methods: ['POST'])]
    public function register(Request $request,EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, CsrfTokenManagerInterface $csrfTokenManager){
        $data = json_decode($request -> getContent(),true);

        $csrfToken = new CsrfToken('register', $data['_token']);

        if (!$csrfTokenManager->isTokenValid($csrfToken)) {
            return new JsonResponse(['error' => 'Invalid CSRF token'], 403);
        }


        $pseudo = $data['pseudo'];
        $email = $data['email'];
        $age = $data['age'];
        $password = $data['password'];
        $ip = $request->getClientIp();
        $iphash = hash('sha256', $ip);

        $user = new Utilisateurs();
        $user->setPseudo($pseudo);
        $user->setEMAIL($email);
        $user->setAGE($age);
        $user->setSUBSCRIBERS(0);
        $user->setUPLOADEDVIDEO(0);
        $user->setISADMIN(false);
        $user->setJOINDATE(new \DateTime());
        $user->setIPADRESSE($iphash);
        $hashedpassword = $passwordHasher -> hashPassword($user, $password);
        $user->setPASSWORD($hashedpassword);
        $em -> persist($user);
        $em->flush();


        $request -> getSession()->set('user_id', $user->getId());
        return new JsonResponse(['message' => 'inscription réussie']);



    }




}
