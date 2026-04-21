<?php

namespace App\Controller;

use App\Repository\VideoRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WatchController extends AbstractController
{
    #[Route('/watch/{uuid}', name: 'app_watch', requirements: ['uuid'=>'[0-9a-fA-F]{32}'],  methods: ['GET'])]
    public function watchvideo($uuid, VideoRepository $videorepo): Response
    {
        $video = $videorepo->findOneBy(['uuid' => $uuid]);

        return $this->render('watch/watch.html.twig', [
            'video' => $video
        ]);
    }

#[Route('/short/{uuid}', name: 'app_short', requirements: ['uuid'=>'[0-9a-fA-F]{32}'],  methods: ['GET'])]
public function watchshort($uuid, VideoRepository $videorepo): Response{

    $short = $videorepo->findOneBy(['uuid' => $uuid, 'isShort' => true]);




    return $this->render('watch/short.html.twig', [
        'short' => $short

    ]);


}

}
