<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Video;
use App\Form\VideoUploadType;

final class UploadsController extends AbstractController
{
    #[Route('/videoupload', name: 'video_uploads')]
    public function video(): Response
    {
        return $this->render('video_upload.html.twig', [
            'controller_name' => 'UploadsController',
        ]);
    }

    #[Route('/shortupload', name: 'short_uploads_form')]
    public function short(): Response
    {
        return $this->render('short_upload.html.twig', [
            'controller_name' => 'UploadsController',
        ]);
    }


    #[Route('/createvideo', name: 'video_create_form')]
    public function createvideo(): Response
    {
        return $this->render('short_upload.html.twig', [
            'controller_name' => 'UploadsController',
            "upload_form" =>
        ]);
    }
}
