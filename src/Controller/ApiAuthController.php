<?php

namespace App\Controller;

use App\Entity\Utilisateurs;
use App\Form\RegisterType;
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
use Symfony\Component\Uid\Uuid;

final class ApiAuthController extends AbstractController
{
    #[Route('/api/login', name: 'app_api_auth_login',methods: ['POST'])]
    public function login(Request $request, UtilisateursRepository $UTILISATEURSRepository, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $data = json_decode($request -> getContent(), true);
        $email = $data['email'];
        $password = $data['password'];
        $user = $UTILISATEURSRepository->findOneBy(['EMAIL' => $email]);

        if (!$user || !$passwordHasher->isPasswordValid($user, $password)){
            return new JsonResponse(['message' => "Email ou mot de passe invalide veuillez re essayer,merci"]);
        }

        $request -> getSession()->set('user_id', $user->getId());
        return new JsonResponse(['message' => 'Connexion réussie']);
    }

    #[Route('/api/register', name: 'app_api_auth_register',methods: ['POST'])]
    public function register(Request $request,EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, CsrfTokenManagerInterface $csrfTokenManager){


        $user = new Utilisateurs();



        $ip = $request->getClientIp();
        $iphash = hash('sha256', $ip);
        $user->setSUBSCRIBERS(0);
        $user->setUPLOADEDVIDEO(0);
        $user->setISADMIN(false);
        $user->setJOINDATE(new \DateTime());


        $registerForm = $this->createForm(RegisterType::class, $user);
        $registerForm->handleRequest($request);


        if($registerForm->isSubmitted()&&$registerForm->isValid()){
            $hashedpassword = $passwordHasher -> hashPassword($user, $user->getPassword());

            $user->setPASSWORD($hashedpassword);
            $user -> setUuid(Uuid::v7()->toRfc4122());
            $em->persist($user);
            $em->flush();
            $user->setIPADRESSE($iphash);
            return new JsonResponse(['message' => 'inscription réussie']);
        }else {
            // récupère les erreurs du formulaire
            $errors = [];
            foreach ($registerForm->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }        //dd($request->request->all(), $registerForm->isSubmitted(), $registerForm->isValid());

            return new JsonResponse(['message' => 'Erreur', 'errors' => $errors], 400);
        }



    }




}
