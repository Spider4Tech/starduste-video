<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Form\RegisterType;
use App\Repository\CommentsRepository;
use App\Repository\UtilisateursRepository;
use App\Repository\VideoRepository;
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
use Symfony\Component\Validator\Validator\ValidatorInterface;


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



                    return $this->redirectToRoute('app_index');

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
                    return $this->redirectToRoute('app_index');
                }


            } else {
                return new JsonResponse(['error' => $uploadform->getErrors(), 'message' => "le fichier uploadé n'est pas une video"]);
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
    public function comment_upload(Request $request, EntityManagerInterface $em, ValidatorInterface $validator, CommentsRepository $commentsRepository, VideoRepository $videoRepository): JsonResponse
    {

        $data = json_decode($request->getContent(), true);
        if ($data !== null && isset($data['uuidvideo']) && isset($data['message'])) {
            $uuidVideo = $data['uuidvideo'];
            $ComVideo = $videoRepository->findOneBy(['id' => $uuidVideo]);
            if ($ComVideo === null) {
                return new JsonResponse(['error' => 'Video not found'], 404);
            }
            $user = $this->getUser();
            if (!$user instanceof Utilisateurs) {
                return new JsonResponse(['error' => 'Invalid user type'], 401);
            }

            $Commentaire = new Comments();
            $Commentaire->setCommentaire($data['message']);
            $Commentaire->setComVideo($ComVideo);
            $Commentaire->setCommentUploader($user);
            $Commentaire->setDateComment(new \DateTime());
            $em->persist($Commentaire);
            $em->flush();
            return new JsonResponse(['message' => 'upload du commentaire reussi !!']);
        }
        else{

            return new JsonResponse(['Message' => 'problème de reception des données, peut être que les données sont vides ou malformées']);
        }
    }


    #[Route('/pfpupload', name: 'profilepicture_upload')]
    public function profilepictureupload(Request $request, EntityManagerInterface $em, UtilisateursRepository $utilisateursRepository): JsonResponse
    {
        $projectDir = $this->getParameter('kernel.project_dir');
        $user = $this->getUser();

        $file = $request->files->get('profilepicture');

        if (!$file){
            return new JsonResponse(['success'=> false, 'error'=> 'Aucun fichier reçu']);
        }

        $filename = uniqid() . '.webp';
        $destination = $projectDir . '/public/uploads/ProfilePictures/' . $user->getId();
        if (!is_dir($destination)){
            mkdir($destination, 0775, true);

        }$file->move($destination, $filename);
        $oldpath = $user->getPfppath();
        if ($oldpath) {
            $fullOldPath = $projectDir . '/public/' . $oldpath;
            if (file_exists($fullOldPath)) {
                unlink($fullOldPath);

            }
        }

        $user->setPfppath('uploads/ProfilePictures/'.$user->getId().'/'.$filename);
        $em->flush();

        return new JsonResponse(['success'=>true]);
    }

    #[Route('/bannerupload', name: 'banner_upload')]
    public function bannerupload(Request $request, EntityManagerInterface $em, UtilisateursRepository $utilisateursRepository): JsonResponse
    {
        $projectDir = $this->getParameter('kernel.project_dir');
        $user = $this->getUser();

        $file = $request->files->get('banner');

        if (!$file){
            return new JsonResponse(['success'=> false, 'error'=> 'Aucun fichier reçu']);
        }

        $filename = uniqid() . '.webp';
        $destination = $projectDir . '/public/uploads/Banner/' . $user->getId();
        if (!is_dir($destination)){
            mkdir($destination, 0775, true);

        }$file->move($destination, $filename);
        $oldpath = $user->getBannerpath();
        if ($oldpath) {
            $fullOldPath = $projectDir . '/public/' . $oldpath;
            if (file_exists($fullOldPath)) {
                unlink($fullOldPath);

            }
        }

        $user->setBannerpath('uploads/Banner/'.$user->getId().'/'.$filename);
        $em->flush();

        return new JsonResponse(['success'=>true]);
    }







}
