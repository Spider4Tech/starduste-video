<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Utilisateurs;
use App\Form\RegisterType;
final class DefaultController extends AbstractController
{
    #[Route('/oldlogin', name: 'oldapp_login')]
    public function login(): Response
    {
        return $this->render('oldlogin.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }


    #[Route('/register', name: 'app_register')]
    public function register(): Response
    {
        $user = new Utilisateurs();

        $registerForm = $this->createForm(RegisterType::class, $user);

        return $this->render('register.html.twig',[
           "registerform" => $registerForm->createView()
        ]);




    }

    #[Route('/', name: 'app_index')]
public function index(): Response{


        return $this->render('acceuil.html.twig');
    }

    #[Route('/watch', name: 'video_watch')]
    public function watch(): Response{


        return $this->render('/video_test/watch.html.twig');
    }


    #[Route('/testsession', name: 'sessiontest')]
    public function testsession(Request $request): Response{
        $session = $request->getSession();

        $alldata = $session->all();


        return new Response('<pre>'. print_r($alldata, true).'</pre>');
    }


    #[Route('/testlogin', name: 'logintest')]
    public function logintest(): Response{


        return $this->render('/test/logintest.html.twig');
    }





}
