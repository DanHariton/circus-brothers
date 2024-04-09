<?php

namespace App\Controller\Site;

use App\Entity\Merch;
use App\Repository\ConcertRepository;
use App\Repository\MediaContentRepository;
use App\Repository\MerchRepository;
use App\Service\MediaContent\MediaContentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/", name: "app_")]
class AppController extends AbstractController
{

    public function __construct(
        private readonly ConcertRepository $concertRepository,
        private readonly MerchRepository $merchRepository
    )
    {
    }

    #[Route("/", name: "index")]
    public function index(): Response
    {
        $concerts = $this->concertRepository->findFutureConcerts(1);
        $merchItems = $this->merchRepository->findWithLimitCount(3);

        return $this->render('site/app/index.html.twig', [
            'concerts'    => $concerts,
            'merchItems' => $merchItems
        ]);
    }

    #[Route("/merch", name: "merch")]
    public function merch(): Response
    {
        return $this->render('site/app/merch.html.twig', [
           'merchItems' => $this->merchRepository->findActiveMerch()
        ]);
    }

    #[Route("/contact", name: "contact")]
    public function contact(): Response
    {
        return $this->render('site/app/contact.html.twig');
    }

    #[Route("/merch/{merch}", name: "merch_detail")]
    public function merchDetail(Merch $merch): Response
    {
        $merchItems = $this->merchRepository->findWithLimitCount(3, $merch->getId());

        return $this->render('site/app/merch_detail.html.twig', [
            'merch' => $merch,
            'merchItems' => $merchItems
        ]);
    }

    #[Route("/concerts", name: "concerts")]
    public function concerts(): Response
    {
        $futureConcerts = $this->concertRepository->findFutureConcerts();
        $pastConcerts = $this->concertRepository->findPastConcerts();

        return $this->render('site/app/concerts.html.twig', [
            'futureConcerts' => $futureConcerts,
            'pastConcerts' => $pastConcerts
        ]);
    }

    #[Route("/photo-and-video", name: "photo_and_video")]
    public function photoAndVideo(MediaContentRepository $mediaContentRepository, MediaContentService $contentService): Response
    {
        $mediaContentItems = $mediaContentRepository->findActiveContentSortedByPosition();

        $mediaContentItems = $contentService->sortMediaContentByPattern($mediaContentItems);

        return $this->render('site/app/photo_and_video.html.twig', [
            'mediaContentItems' => $mediaContentItems
        ]);
    }

    #[Route("/show-rider", name: "show_rider")]
    public function showRider(): BinaryFileResponse|Response
    {
        $pdfPath = $this->getParameter('kernel.project_dir') . '/public/rider/RIDER.pdf';

        return $this->file($pdfPath, 'RIDER - technicky.pdf', ResponseHeaderBag::DISPOSITION_INLINE);
    }
}