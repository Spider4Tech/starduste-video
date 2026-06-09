<?php

namespace App\Controller;

use App\Entity\LiveStream;
use App\Entity\Utilisateurs;
use App\Repository\LiveStreamRepository;
use App\Repository\UtilisateursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LiveController extends AbstractController
{
    #[Route('/lives', name: 'app_lives', methods: ['GET'])]
    public function index(LiveStreamRepository $liveStreamRepository): Response
    {
        return $this->render('live/index.html.twig', [
            'lives' => $liveStreamRepository->findLiveStreams(30),
        ]);
    }

    #[Route('/live/key/generate', name: 'live_key_generate', methods: ['POST'])]
    public function generateKey(Request $request, EntityManagerInterface $em): RedirectResponse
    {
        $user = $this->requireUser();
        $force = $request->request->getBoolean('regenerate');

        if ($force || !$user->getLiveStreamKey()) {
            $user->setLiveStreamKey($this->newLiveKey());
            $em->flush();
        }

        return $this->redirectToRoute('app_index');
    }

    #[Route('/live/start', name: 'live_start', methods: ['POST'])]
    public function start(Request $request, EntityManagerInterface $em, LiveStreamRepository $liveStreamRepository): RedirectResponse
    {
        $user = $this->requireUser();

        if (!$user->getLiveStreamKey()) {
            $user->setLiveStreamKey($this->newLiveKey());
        }

        $live = $liveStreamRepository->findOneBy(['streamer' => $user]) ?? new LiveStream();
        if (!$live->getId()) {
            $live->setSlug(bin2hex(random_bytes(16)));
            $live->setStreamer($user);
            $em->persist($live);
        }

        $title = trim((string) $request->request->get('title', 'Live de ' . $user->getPseudo()));
        $category = trim((string) $request->request->get('category', ''));
        $playbackUrl = trim((string) $request->request->get('playback_url', ''));
        $thumbnailPath = trim((string) $request->request->get('thumbnail_path', ''));
        $playbackUrl = $playbackUrl !== '' ? $playbackUrl : $this->hlsUrlForKey($user->getLiveStreamKey());

        $live
            ->setTitle($title !== '' ? substr($title, 0, 128) : 'Live de ' . $user->getPseudo())
            ->setCategory($category !== '' ? substr($category, 0, 80) : null)
            ->setPlaybackUrl(substr($playbackUrl, 0, 255))
            ->setThumbnailPath($thumbnailPath !== '' ? substr($thumbnailPath, 0, 255) : null)
            ->setUpdatedAt(new \DateTime());

        $em->flush();

        return $this->redirectToRoute('app_live_watch', ['slug' => $live->getSlug()]);
    }

    #[Route('/live/stop', name: 'live_stop', methods: ['POST'])]
    public function stop(EntityManagerInterface $em, LiveStreamRepository $liveStreamRepository): RedirectResponse
    {
        $user = $this->requireUser();
        $live = $liveStreamRepository->findOneBy(['streamer' => $user]);

        if ($live) {
            $live
                ->setLive(false)
                ->setViewers(0)
                ->setUpdatedAt(new \DateTime());
            $em->flush();
        }

        return $this->redirectToRoute('app_index');
    }

    #[Route('/live/{slug}', name: 'app_live_watch', requirements: ['slug' => '[0-9a-fA-F]{32}'], methods: ['GET'])]
    public function watch(string $slug, EntityManagerInterface $em, LiveStreamRepository $liveStreamRepository): Response
    {
        $live = $liveStreamRepository->findOneBy(['slug' => $slug]);

        if (!$live) {
            throw $this->createNotFoundException('Live introuvable.');
        }

        if ($live->isLive()) {
            $live->setViewers($live->getViewers() + 1);
            $em->flush();
        }

        return $this->render('live/watch.html.twig', [
            'live' => $live,
            'rtmpUrl' => $this->rtmpUrl(),
        ]);
    }

    #[Route('/live/rtmp/publish', name: 'live_rtmp_publish', methods: ['GET', 'POST'])]
    public function rtmpPublish(
        Request $request,
        EntityManagerInterface $em,
        UtilisateursRepository $utilisateursRepository,
        LiveStreamRepository $liveStreamRepository
    ): Response {
        $streamKey = $this->extractStreamKey($request);
        if ($streamKey === '') {
            return new Response('missing stream key', Response::HTTP_FORBIDDEN);
        }

        $user = $utilisateursRepository->findOneBy(['liveStreamKey' => $streamKey]);
        if (!$user) {
            return new Response('invalid stream key', Response::HTTP_FORBIDDEN);
        }

        $live = $liveStreamRepository->findOneBy(['streamer' => $user]) ?? new LiveStream();
        if (!$live->getId()) {
            $live
                ->setSlug(bin2hex(random_bytes(16)))
                ->setStreamer($user)
                ->setTitle('Live de ' . $user->getPseudo());
            $em->persist($live);
        }

        $now = new \DateTime();
        if (!$live->isLive()) {
            $live->setStartedAt($now);
        }

        $live
            ->setLive(true)
            ->setPlaybackUrl($this->hlsUrlForKey($streamKey))
            ->setUpdatedAt($now);

        $em->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    #[Route('/live/rtmp/publish-done', name: 'live_rtmp_publish_done', methods: ['GET', 'POST'])]
    public function rtmpPublishDone(
        Request $request,
        EntityManagerInterface $em,
        UtilisateursRepository $utilisateursRepository,
        LiveStreamRepository $liveStreamRepository
    ): Response {
        $streamKey = $this->extractStreamKey($request);
        if ($streamKey === '') {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        $user = $utilisateursRepository->findOneBy(['liveStreamKey' => $streamKey]);
        if (!$user) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        $live = $liveStreamRepository->findOneBy(['streamer' => $user]);
        if ($live) {
            $live
                ->setLive(false)
                ->setViewers(0)
                ->setUpdatedAt(new \DateTime());
            $em->flush();
        }

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    private function requireUser(): Utilisateurs
    {
        $user = $this->getUser();
        if (!$user instanceof Utilisateurs) {
            throw $this->createAccessDeniedException('Connexion requise.');
        }

        return $user;
    }

    private function newLiveKey(): string
    {
        return 'sd_live_' . bin2hex(random_bytes(24));
    }

    private function extractStreamKey(Request $request): string
    {
        $streamKey = $request->query->get('name') ?? $request->request->get('name') ?? '';

        return trim((string) $streamKey);
    }

    private function hlsUrlForKey(?string $streamKey): string
    {
        $baseUrl = getenv('LIVE_HLS_PUBLIC_BASE_URL') ?: 'http://localhost:8081/hls';

        return rtrim($baseUrl, '/') . '/' . $streamKey . '.m3u8';
    }

    private function rtmpUrl(): string
    {
        return getenv('LIVE_RTMP_PUBLIC_URL') ?: 'rtmp://localhost:1935/live';
    }
}
