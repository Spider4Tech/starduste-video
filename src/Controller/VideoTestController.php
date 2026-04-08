<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class VideoTestController extends AbstractController
{
    #[Route('/video/test', name: 'app_video_test')]
    public function index(): Response
    {
        return $this->render('video_test/controller.html.twig');
    }
}
