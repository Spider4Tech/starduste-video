<?php

namespace App\Controller;

use App\Entity\Reports;
use App\Repository\RaisonsRepository;
use App\Repository\VideoRepository;
use App\Repository\CommentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ReportController extends AbstractController
{
    #[Route('/report', name: 'app_report')]
    public function index(): Response
    {
        return $this->render('report/index.html.twig', [
            'controller_name' => 'ReportController',
        ]);
    }


    #[Route('/reporting', name: 'reporting', methods: ['POST'])]
    public function reporting(
        Request $request,
        EntityManagerInterface $em,
        RaisonsRepository $raisonsRepository,
        VideoRepository $videoRepository,
        CommentsRepository $commentsRepository
    ): JsonResponse {

        $data = json_decode($request->getContent(), true);

        // Vérifications de base
        if (!isset($data['videoId'], $data['raisonId'], $data['type'])) {
            return $this->json(['error' => 'Données manquantes'], 400);
        }

        $video = $videoRepository->find($data['videoId']);
        if (!$video) {
            return $this->json(['error' => 'Vidéo introuvable'], 404);
        }

        $raison = $raisonsRepository->find($data['raisonId']);
        if (!$raison) {
            return $this->json(['error' => 'Raison introuvable'], 404);
        }

        $comment = null;
        if ($data['type'] === 'comment') {
            if (empty($data['commentId'])) {
                return $this->json(['error' => 'ID commentaire manquant'], 400);
            }
            $comment = $commentsRepository->find($data['commentId']);
            if (!$comment) {
                return $this->json(['error' => 'Commentaire introuvable'], 404);
            }
        }

        $report = new Reports();
        $report->setIdvideo($video);
        $report->setRaison($raison);
        $report->setType($data['type']);
        $report->setIdcommentaire($comment);
        $report->setPrecisionUser($data['precision'] ?? null);
        $report->setIdUser($this->getUser());
        $report->setTraite(false);
        $report->setDatecreation(new \DateTimeImmutable());

        $em->persist($report);
        $em->flush();

        return $this->json(['success' => true, 'message' => 'Report soumis']);
    }
}
