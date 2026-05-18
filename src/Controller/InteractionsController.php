<?php

namespace App\Controller;

use App\Entity\Opinion;
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

            return new JsonResponse(['action' => 'unliked']);

        } else if ($like == null or $like->getValue() == 'Disliked') {//la en l'occurence si $like n'est pas null on va ajouter à la badd
            //l'entrée du like de la personne et puis voila :D
            $Opinion = new Opinion();
            $Opinion->setUserId($user);
            $Opinion->setCreatedAt(new \DateTime());
            $Opinion->setVideoId($Video);
            $Opinion->setValue("Liked");
            $Video->setLikeVid($Video->getLikeVid() + 1);
            if ($like and $like->getValue() == 'Disliked') {

                $em->remove($like);
                $Video->setDislikeVid($Video->getDislikeVid() -1);
                $em->flush();
            }
            $em->persist($Opinion);
            $em->flush();
            return new JsonResponse(['action' => 'Liked']);


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

            return new JsonResponse(['action' => 'undisliked']);

        } else if ($dislike == null or $dislike->getValue() == 'Liked') {//la en l'occurence si $dislike n'est pas null on va ajouter à la badd
            //l'entrée du like de la personne et puis voila :D
            $Opinion = new Opinion();
            $Opinion->setUserId($user);
            $Opinion->setCreatedAt(new \DateTime());
            $Opinion->setVideoId($Video);
            $Opinion->setValue("Disliked");
            $Video->setDislikeVid($Video->getDislikeVid() +1);
            if ($dislike and $dislike->getValue() == 'Liked') {
                $em->remove($dislike);
                $Video->setLikeVid($Video->getLikeVid() - 1);
                $em->flush();
            }
            $em->persist($Opinion);
            $em->flush();
            return new JsonResponse(['action' => 'Disliked']);


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
}
