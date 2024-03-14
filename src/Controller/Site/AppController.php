<?php

namespace App\Controller\Site;

use App\Entity\Merch;
use App\Repository\ConcertRepository;
use App\Repository\MerchRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/", name: "app_")]
class AppController extends AbstractController
{
    public function __construct(private readonly ConcertRepository $concertRepository, private readonly MerchRepository $merchRepository)
    {
    }

    #[Route("/", name: "index")]
    public function index(): Response
    {
        $concerts = $this->concertRepository->findFutureConcerts(1);
        $merchs = $this->merchRepository->findWithLimitCount(3);

        return $this->render('site/app/index.html.twig', [
            'concerts' => $concerts,
            'merchs'   => $merchs
        ]);
    }

    #[Route("/merch", name: "merch")]
    public function merch(): Response
    {
        return $this->render('site/app/merch.html.twig', [
           'merchs' => $this->merchRepository->findAll()
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
        $merchs = $this->merchRepository->findWithLimitCount(3, $merch->getId());

        return $this->render('site/app/merch_detail.html.twig', [
            'merch' => $merch,
            'merchs' => $merchs
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
}