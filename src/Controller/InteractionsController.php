<?php

namespace App\Controller;

use App\Entity\Opinion;
use App\Repository\CommentsRepository;
use App\Repository\OpinionRepository;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class InteractionsController extends AbstractController
{
    #[Route('/addvideolike', name: 'add_like_video')]
    public function index(EntityManagerInterface $em, VideoRepository $videoRepository, OpinionRepository $opinionRepository, Request $request):JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if ($data == null) {
            return new JsonResponse(["error" => "apparement data a l'air null"]);
        }
        $uuidVideo = $data['idVideo'];
        $like = $opinionRepository->findOneBy(['video_id' => $uuidVideo]);
        $Video = $videoRepository->findOneBy(['id' => $uuidVideo]);
        $user = $this->getUser();

        if ($like != null and $like->getValue() == 'Liked') //la on regarde si ce qu'a repondu doctrine est null, si c'est pas le cas
            //alors la vidéo a bien été liké donc du coup on va enlever ce like techniquement
        {
            $em->remove($like);
            $Video->setLikeVid($Video->getLikeVid() - 1);
            $em->flush();

            return new JsonResponse(['action' => 'unliked', 'nombrelike'=>$Video->getLikeVid()]);

        } else if ($like == null or $like->getValue() == 'Disliked') {//la en l'occurence si $like n'est pas null on va ajouter à la badd
            //l'entrée du like de la personne et puis voila :D
            $Opinion = new Opinion();
            $Opinion->setUserId($user);
            $Opinion->setCreatedAt(new \DateTime());
            $Opinion->setVideoId($Video);
            $Opinion->setValue("Liked");
            $Opinion->setType("VIDEO");
            $Video->setLikeVid($Video->getLikeVid() + 1);
            if ($like and $like->getValue() == 'Disliked') {

                $em->remove($like);
                $Video->setDislikeVid($Video->getDislikeVid() -1);
                $em->flush();
            }
            $em->persist($Opinion);
            $em->flush();
            return new JsonResponse(['action' => 'Liked', 'nombredislike'=> $Video->getDislikeVid(), 'nombrelike'=> $Video->getLikeVid()]);


        }
        return new JsonResponse(['error' => 'une erreur est survenue lors de l\'ajout du like']);

    }


    //TODO reprendre le controller addvideolike mais cette fois tu le fais avec les dislike ça ne te prendra pas longtemps à faire
    //tu a juste besoins d'inverser certaines choses et aussi pense a bloquer les likes/dislike pour les gens non connecté :D

    #[Route('/addvideodislike', name: 'add_dislike_video')]
    public function dislike(EntityManagerInterface $em, VideoRepository $videoRepository, OpinionRepository $opinionRepository, Request $request):JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if ($data == null) {
            return new JsonResponse(["error" => "apparement data a l'air null"]);
        }
        $uuidVideo = $data['idVideo'];
        $dislike = $opinionRepository->findOneBy(['video_id' => $uuidVideo]);
        $Video = $videoRepository->findOneBy(['id' => $uuidVideo]);
        $user = $this->getUser();

        if ($dislike != null and $dislike->getValue() == 'Disliked') //la on regarde si ce qu'a repondu doctrine est null, si c'est pas le cas
            //alors la vidéo a bien été disliké donc du coup on va enlever ce dislike techniquement
        {
            $em->remove($dislike);
            $Video->setDislikeVid($Video->getDislikeVid() -1);
            $em->flush();

            return new JsonResponse(['action' => 'undisliked', 'nombredislike' => $Video->getDislikeVid()]);

        } else if ($dislike == null or $dislike->getValue() == 'Liked') {//la en l'occurence si $dislike n'est pas null on va ajouter à la badd
            //l'entrée du like de la personne et puis voila :D
            $Opinion = new Opinion();
            $Opinion->setUserId($user);
            $Opinion->setCreatedAt(new \DateTime());
            $Opinion->setVideoId($Video);
            $Opinion->setValue("Disliked");
            $Opinion->setType("VIDEO");
            $Video->setDislikeVid($Video->getDislikeVid() +1);
            if ($dislike and $dislike->getValue() == 'Liked') {
                $em->remove($dislike);
                $Video->setLikeVid($Video->getLikeVid() - 1);
                $em->flush();
            }
            $em->persist($Opinion);
            $em->flush();
            return new JsonResponse(['action' => 'Disliked', 'nombredislike'=> $Video->getDislikeVid(), 'nombrelike'=> $Video->getLikeVid()]);


        }
        return new JsonResponse(['error' => 'une erreur est survenue lors de l\'ajout du like']);

    }

    #[Route('/checklike', name: 'checklike')]
    public function checklike(EntityManagerInterface $em, VideoRepository $videoRepository, OpinionRepository $opinionRepository, Request $request):JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->getuser();
        $uuidvideo = $data['idvideo'];
        $Opinion = $opinionRepository -> findOneBy(['video_id' => $uuidvideo]);

        if ($Opinion->getValue()== "Liked"){
            return new JsonResponse(['action' => 'Liked']);

        }
        elseif ($Opinion->getValue() == "Disliked"){
            return new JsonResponse(['action' => 'Disliked']);
        }
        elseif (!$Opinion) {
            return new JsonResponse(['action' => 'nope']);
        }


        return new JsonResponse(['error' => 'problème avec la requête à la base']);

    }


#[Route('/addcommentarylike', name: 'commentarylike')]
public function commentarylike(EntityManagerInterface $em, OpinionRepository $opinionRepository, Request $request, CommentsRepository $commentsRepository, VideoRepository $videoRepository):JsonResponse{
        $data = json_decode($request->getContent(), true);
        if ($data == null) {
             return new JsonResponse(["error" => "apparement data a l'air null"]);
        }
        $uuidVideo = $data['idVideo'];
        $idcommentaire = $data['idcommentaire'];
        $user = $this->getUser();

        $Video = $videoRepository->findOneBy(['id' => $uuidVideo]);
        $commentaire = $commentsRepository->findOneBy(['id' => $idcommentaire]);
        $like = $opinionRepository->findOneBy(['Commentid' => $commentaire,
            'user_id' => $user,
            'type' => 'COMMENT']);
        $user = $this->getUser();

    if ($like != null and $like->getValue() == 'Liked')
    {
        $em->remove($like);
        $commentaire->setComlike($commentaire->getComlike()-1);
        $em->flush();

        return new JsonResponse(['action' => 'unliked', 'nombrelike' => $commentaire->getComlike()]);
    }

        if($like == null or $like->getValue() == 'Disliked') {


            if ($like and $like->getValue() == 'Disliked') {
            $commentaire->setComdislike($commentaire->getComdislike()-1);

            $em->remove($like);



            }
            $Opinion = new Opinion();
            $Opinion->setUserId($user);
            $Opinion->setCreatedAt(new \DateTime());
            $Opinion->setVideoId($Video);
            $Opinion->setValue("Liked");
            $Opinion->setType("COMMENT");
            $Opinion->setCommentid($commentaire);
            $commentaire->setComlike($commentaire->getComlike()+1);

            $em->persist($Opinion);
            $em->flush();
            return new JsonResponse(['action' => 'Liked', 'nombrelike' => $commentaire->getComlike(), 'nombredislike' => $commentaire->getComdislike()]);
        }



    return new JsonResponse(['error' => 'une erreur est survenue lors de l\'ajout du like du commentaire']);

        //TODO faire l'api pour les likes de commentaires (donc même principe qu'avec les vidéos mais cette fois avec les coms du coup type = COM au lieux de VIDEO
}

    #[Route('/addcommentarydislike', name: 'commentarydislike')]
    public function commentarydislike(EntityManagerInterface $em, OpinionRepository $opinionRepository, Request $request, CommentsRepository $commentsRepository, VideoRepository $videoRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if ($data == null) {
            return new JsonResponse(['error' => 'data null']);
        }

        $idcommentaire = $data['idcommentaire'];
        $commentaire = $commentsRepository->findOneBy(['id' => $idcommentaire]);
        $user = $this->getUser();

        $dislike = $opinionRepository->findOneBy([
            'Commentid' => $commentaire,
            'user_id' => $user,
            'type' => 'COMMENT'
        ]);

        if ($dislike != null && $dislike->getValue() == 'Disliked') {
            // Annule le dislike
            $em->remove($dislike);
            $commentaire->setComdislike($commentaire->getComdislike()-1);
            $em->flush();
            return new JsonResponse(['action' => 'undisliked', 'nombredislike'=>$commentaire->getComdislike()]);
        }

        if ($dislike != null && $dislike->getValue() == 'Liked') {
            // Change le like en dislike
            $em->remove($dislike);
            $commentaire->setComlike($commentaire->getComlike()-1);
            $em->flush();
        }

        $opinion = new Opinion();
        $opinion->setUserId($user);
        $opinion->setCommentid($commentaire);
        $opinion->setValue('Disliked');
        $opinion->setType('COMMENT');
        $opinion->setCreatedAt(new \DateTime());
        $commentaire->setComdislike($commentaire->getComdislike()+1);

        $em->persist($opinion);
        $em->flush();

        return new JsonResponse(['action' => 'Disliked', 'nombrelike' => $commentaire->getComlike(), 'nombredislike' => $commentaire->getComdislike(),]);
    }

    #[Route('/checkcommentlikes', name: 'check_comment_likes', methods: ['POST'])]
    public function checkCommentLikes(Request $request, OpinionRepository $opinionRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $commentIds = $data['commentIds'] ?? [];
        $user = $this->getUser();

        if (!$user || empty($commentIds)) {
            return new JsonResponse(['liked' => [], 'disliked' => []]);
        }

        $opinions = $opinionRepository->findBy([
            'user_id' => $user,
            'type' => 'COMMENT'
        ]);

        $liked = [];
        $disliked = [];

        foreach ($opinions as $opinion) {
            $comId = $opinion->getCommentid()?->getId();
            if (in_array((string)$comId, $commentIds)) {
                if ($opinion->getValue() === 'Liked') $liked[] = $comId;
                if ($opinion->getValue() === 'Disliked') $disliked[] = $comId;
            }
        }

        return new JsonResponse(['liked' => $liked, 'disliked' => $disliked]);
    }
}
