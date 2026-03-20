<?php

namespace App\Controller;

use App\Form\RegisterType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Video;
use App\Form\VideoUploadType;
use Symfony\Component\Filesystem\Filesystem;

final class UploadsController extends AbstractController
{
    #[Route('/api/VideoUpload', name: 'video_uploads')]
    public function uploadVideo(Request $request): Response
    {
        $ffprobe = \FFMpeg\FFProbe::create();
        $filesystem = new Filesystem();
        $projectDir = $this->getParameter('kernel.project_dir');

        $uploadform = $this->createForm(VideoUploadType::class);
        $uploadform->handleRequest($request);
        if ($uploadform->isSubmitted() && $uploadform->isvalid()) {
            $file = $uploadform->get('videoFile')->getData();

            $fluxvideo = $ffprobe->streams($file->getPathname())->videos()->first();
            $width = $fluxvideo->get('width');
            $height = $fluxvideo->get('height');

            if ($file and $file->isValid()) {
                $newfilename = bin2hex(random_bytes(16));
                if ($height > $width) {
                    $destination = $projectDir . '/public/uploads/shorts/' . $newfilename;
                    if (!is_dir($destination)) {
                        mkdir($destination, 0775, true);
                    }
                    $extension = $file->guessExtension();
                    $newfilename = $newfilename . '.' . $extension;
                    $file->move($destination, $newfilename);
                    return new JsonResponse(['message' => 'upload short reussi']);

                }
                else {
                    $destination = $projectDir . '/public/uploads/videos/' . $newfilename;

                    if (!is_dir($destination)) {
                        mkdir($destination, 0775, true);
                    }
                    $extension = $file->guessExtension();
                    $newfilename = $newfilename . '.' . $extension;
                    $file->move($destination, $newfilename);
                    return new JsonResponse(['message' => 'upload reussi']);
                }


            } else {
                return new JsonResponse(['error' => $uploadform->getErrors(), 'message' => "le fichier uploadé n'est pas une videosd"]);
            }
        }
        if (!$uploadform->isSubmitted() || !$uploadform->isValid()) {
            $errors = [];
            foreach ($uploadform->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }
            return $this->json(['valid' => false, 'errors' => $errors], 400);
        }
        return $this->json(['message' => 'Aucun fichier soumis'], 400);


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
        $registerForm = $this->createForm(VideoUploadType::class);
        return $this->render('/uploads/createvideo.html.twig', [
            'controller_name' => 'UploadsController',
            'uploadForm' => $registerForm->createView(),

        ]);
    }
}
