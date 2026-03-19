<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Video;
use App\Form\VideoUploadType;

final class UploadsController extends AbstractController
{
    #[Route('/api/VideoUpload', name: 'video_uploads')]
    public function uploadVideo(Request $request): Response
    {
        $uploadform = $this->createForm(VideoUploadType::class);
        $uploadform->handlerequest($request);
        if ($uploadform->isSubmitted() && $uploadform->isvalid())
        $file = $uploadform->get('videoFile')->getData();
        if ($file){
            $newfilename = bin2hex(random_bytes(16));
            $destination =
            $file->move();


        }
        else{

        }









        return $this->render('.html.twig', [
            'controller_name' => 'UploadsController',
            'video' => $file
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
            //"upload_form" =>
        ]);
    }
}
