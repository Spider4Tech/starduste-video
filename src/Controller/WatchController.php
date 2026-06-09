<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Form\CommentType;
use App\Repository\CommentsRepository;
use App\Repository\RaisonsRepository;
use App\Repository\VideoRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WatchController extends AbstractController
{
    #[Route('/watch/{uuid}', name: 'app_watch', requirements: ['uuid'=>'[0-9a-fA-F]{32}'],  methods: ['GET'])]
    public function watchvideo($uuid, VideoRepository $videorepo, CommentsRepository $commentsRepository, RaisonsRepository $raisonsRepository): Response
    {
        $video = $videorepo->findOneBy(['uuid' => $uuid]);

        $comment = new Comments();

        $Commentform = $this->createForm(CommentType::class, $comment , [
            'action' => $this->generateUrl('comment_upload', ['uuid' => $uuid]),
            'method' => 'POST',
        ]);

        $segments = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
        $raisons = $raisonsRepository->findAll();

        $comments = $commentsRepository->findBy(['ComVideo' => $video->getId()]);

        $commentsData = [];
        foreach ($comments as $comment){
            $commentsData[] = [
                'id' => $comment->getId(),
                'ComText' => $comment->getCommentaire(),
                'Likes' => $comment->getComlike(),
                'Dislikes' => $comment->getComdislike(),
                'Date' => $comment->getDateComment(),
                'uploaderpfp' => $comment->getCommentUploader()->getPfppath(),
                'uploaderUsername' => $comment->getCommentUploader()->getPseudo()





            ];
        }
        $video->setViews($video->getViews()+1);


        return $this->render('watch/watch.html.twig', [
            'video' => $video,
            'Commentaires' => $commentsData,
            'Raisons' => $raisons,
            'CommentForm' => $Commentform->createView(),
        ]);
    }

#[Route('/short/{uuid}', name: 'app_short', requirements: ['uuid'=>'[0-9a-fA-F]{32}'],  methods: ['GET'])]
public function watchshort($uuid, VideoRepository $videorepo, CommentsRepository $commentsRepository): Response{

    $short = $videorepo->findOneBy(['uuid' => $uuid, 'isShort' => true]);

    $comment = new Comments();

    $Commentform = $this->createForm(CommentType::class, $comment , [
        'action' => $this->generateUrl('comment_upload', ['uuid' => $uuid]),
        'method' => 'POST',
    ]);

    $segments = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));

    $comments = $commentsRepository->findBy(['ComVideo' => $short->getId()]);
    $commentsData = [];
    foreach ($comments as $comment){
        $commentsData[] = [
            'id' => $comment->getId(),
            'ComText' => $comment->getCommentaire(),
            'Likes' => $comment->getComlike(),
            'Dislikes' => $comment->getComdislike(),
            'Date' => $comment->getDateComment(),
            'uploaderpfp' => $comment->getCommentUploader()->getPfppath(),
            'uploaderUsername' => $comment->getCommentUploader()->getPseudo()





        ];
    }
    $short->setViews($short->getViews()+1);




    return $this->render('watch/short.html.twig', [
        'short' => $short,
        'Commentaires' => $commentsData,
            'CommentForm' => $Commentform->createView(),

    ]);


}

}
