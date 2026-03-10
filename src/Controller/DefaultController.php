<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DefaultController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(): Response
    {
        return $this->render('login.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }


    #[Route('/register', name: 'app_register')]
    public function register(): Response
    {
        return $this->render('register.html.twig',[
           'controller_name' => 'DefaultController'
        ]);




    }

    #[Route('/', name: 'app_index')]
public function index(): Response{


        return $this->render('base_video.html.twig');
    }



}
