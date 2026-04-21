<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Utilisateurs;
use App\Form\RegisterType;
use App\Repository\VideoRepository;
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
public function index(VideoRepository $videoRepository): Response{
        $videos = $videoRepository->findBy(['isShort'=> false, ], ['id' => 'DESC']);
        $videosData = [];
        foreach ($videos as $video){
            $videosData[] = [
              'id' => $video->getId(),
              'uuid'=>$video->getUuid(),
              'title' => $video->getTitle(),
              'duration' => $video->getdurationformatted(),
              'views' => $video->getViews(),
              'thumbnail' => $video->getThumbnail(),
              'uploaderpfp' => $video->getUploader()->getpfppath(),
              'uploaderusername' => $video->getUploader()->getPseudo(),


            ];
        }

        $shorts = $videoRepository->findBy(['isShort'=> true,], ['id'=> 'DESC']);
        $shortsdata = [];
        foreach($shorts as $short){
            $shortsdata[] = [
                'id' => $short->getId(),
                'uuid'=>$short->getUuid(),
                'title' => $short->getTitle(),
                'duration' => $short->getdurationformatted(),
                'views' => $short->getViews(),
                'thumbnail' => $short->getThumbnail(),
                'uploaderpfp' => $short->getUploader()->getPfppath(),
                'uploaderusername' => $short->getUploader()->getPseudo(),

            ];
        }
        return $this->render('acceuil.html.twig', [
            'videos' => $videosData,
            'shorts'=> $shortsdata
        ]);
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
