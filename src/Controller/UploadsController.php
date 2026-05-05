<?php

namespace App\Controller;

use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Video;
use App\Form\VideoUploadType;
use Symfony\Component\Filesystem\Filesystem;
use FFMpeg\FFProbe;
use FFMpeg\FFMpeg;
use App\Entity\Utilisateurs;


final class UploadsController extends AbstractController
{
    #[Route('/api/VideoUpload', name: 'video_uploads')]
    public function uploadVideo(Request $request, EntityManagerInterface $em): Response
    {
        $ffprobe = \FFMpeg\FFProbe::create();
        $filesystem = new Filesystem();
        $projectDir = $this->getParameter('kernel.project_dir');

        $uploadform = $this->createForm(VideoUploadType::class);
        $uploadform->handleRequest($request);
        $user = $this->getUser();
        $userid = $user->getId();
        if ($uploadform->isSubmitted() && $uploadform->isvalid()) {
            $file = $uploadform->get('videoFile')->getData();
            $thumbnail = $uploadform->get('thumbnailFile')->getData();

            $fluxvideo = $ffprobe->streams($file->getPathname())->videos()->first();
            $width = $fluxvideo->get('width');
            $height = $fluxvideo->get('height');
            $duration = $fluxvideo->get('duration');
            $video = new Video();

            if ($file and $file->isValid()) {
                $uuid = bin2hex(random_bytes(16));
                if ($height > $width) {
                    $destination = $projectDir . '/public/uploads/shorts/' . $uuid;
                    if (!is_dir($destination)) {
                        mkdir($destination, 0775, true);
                    }
                    $newthumbnailname = "";
                    $thumbnailPath = "";
                    $extension = $file->guessExtension();
                    $newfilename = $uuid . '.' . $extension;
                    $file->move($destination, $newfilename);
                    $dbpath = 'uploads/shorts/'.$uuid.'/'.$newfilename;
                    if ($thumbnail){
                        $thumbnailextension = $thumbnail->guessExtension();
                        $newthumbnailname = "Thumbnail".$uuid.'.'.$thumbnailextension;
                        $thumbnail->move($destination, $newthumbnailname);
                        $thumbnailPath = 'uploads/shorts/'.$uuid.'/'.$newthumbnailname;
                    }
                    else{
                        $thumbnailPath = "uploads/fallbacksElement/FallbackThumbnail.webp";






                    }
                    $video = new Video();
                    $video->setUuid($uuid);
                    $video->setTitle($uploadform->get('title')->getData());
                    $video->setStatus($uploadform->get('status')->getData());
                    $video->setCategorie($uploadform->get('categorie')->getData());
                    $video->setDescription($uploadform->get('description')->getData());
                    $video->setViews(0);
                    $video->setVideoUrl($dbpath);
                    $video->setLikeVid(0);
                    $video->setDislikeVid(0);
                    $video->setUploadDate(new \DateTime());
                    $video->setThumbnail($thumbnailPath);
                    $video->setIsShort(true);
                    $video->setVideoDuration($duration);
                    $video->setUploader($user);
                    $em->persist($video);
                    $em->flush();


                    return new JsonResponse(['message' => 'upload short reussi']);

                }
                else {
                    $destination = $projectDir . '/public/uploads/videos/' . $uuid;
                    if (!is_dir($destination)) {
                        mkdir($destination, 0775, true);
                    }
                    $newthumbnailname = "";
                    $thumbnailPath = "";
                    $extension = $file->guessExtension();
                    $newfilename = $uuid . '.' . $extension;
                    $file->move($destination, $newfilename);
                    $dbpath = 'uploads/videos/'.$uuid.'/'.$newfilename;
                    if ($thumbnail){
                        $thumbnailextension = $thumbnail->guessExtension();
                        $newthumbnailname = "Thumbnail".$uuid.'.'.$thumbnailextension;
                        $thumbnail->move($destination, $newthumbnailname);
                        $thumbnailPath = 'uploads/videos/'.$uuid.'/'.$newthumbnailname;
                    }
                    else{
                        $thumbnailPath = "uploads/fallbacksElement/FallbackThumbnail.webp";

                    }
                    $video = new Video();
                    $video->setUuid($uuid);
                    $video->setTitle($uploadform->get('title')->getData());
                    $video->setStatus($uploadform->get('status')->getData());
                    $video->setCategorie($uploadform->get('categorie')->getData());
                    $video->setDescription($uploadform->get('description')->getData());
                    $video->setVideoUrl($dbpath);
                    $video->setLikeVid(0);
                    $video->setDislikeVid(0);
                    $video->setUploadDate(new \DateTime());
                    $video->setThumbnail($thumbnailPath);
                    $video->setIsShort(false);
                    $video->setVideoDuration($duration);
                    $video->setUploader($user);
                    $em->persist($video);
                    $em->flush();
                    return new JsonResponse(['message' => 'upload Video reussi']);
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

    #[Route('/commentupload', name: 'comment_upload')]
    public function comment_upload(Request $request, EntityManagerInterface $em): JsonResponse
    {

        $data = json_decode($request->getContent(), true);
        $user = $this->getUser();



        return $this->render('short_upload.html.twig', [
            'controller_name' => 'UploadsController',
        ]);
    }



}
