<?php

namespace App\Controller\Admin;

use App\Entity\Concert;
use App\Form\Concert\ConcertFormType;
use App\Repository\ConcertRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/concert", name: "concert_")]
class ConcertController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    #[Route("/", name: "list")]
    public function list(ConcertRepository $concertRepository): Response
    {
        $concerts = $concertRepository->findBy([], ['date' => 'desc']);

        return $this->render('admin/actions/concert/list.html.twig', [
            'concerts' => $concerts
        ]);
    }

    #[Route("/create", name: "create")]
    public function create(Request $request): RedirectResponse|Response
    {
        $concert = new Concert();

        $form = $this->createForm(ConcertFormType::class, $concert)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Concert $concert */
            $concert = $form->getData();

            $concert->setActive(Concert::STATUS_ACTIVE);
            $concert->setChanged(new DateTime());

            $this->em->persist($concert);
            $this->em->flush();

            return $this->redirectToRoute("concert_list");
        }

        return $this->render('admin/actions/concert/create.html.twig', [
           'form' => $form->createView()
        ]);
    }

    #[Route("/edit/{concert}", name: "edit")]
    public function edit(Concert $concert, Request $request): RedirectResponse|Response
    {
        $form = $this->createForm(ConcertFormType::class, $concert)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Concert $concert */
            $concert = $form->getData();

            $concert->setChanged(new DateTime());

            $this->em->persist($concert);
            $this->em->flush();

            return $this->redirectToRoute("concert_edit", ['concert' => $concert->getId()]);
        }

        return $this->render('admin/actions/concert/edit.html.twig', [
            'form'    => $form->createView(),
            'concert' => $concert
        ]);
    }

    #[Route("/toggle-status/{concert}", name: "toggle_status")]
    public function toggleStatus(Concert $concert): RedirectResponse
    {
        $concert->setActive(!$concert->isActive());

        $this->em->persist($concert);
        $this->em->flush();

        return $this->redirectToRoute('concert_list');
    }

    #[Route("/delete/{concert}", name: "delete")]
    public function delete(Concert $concert): RedirectResponse
    {
        $this->em->remove($concert);
        $this->em->flush();

        return $this->redirectToRoute('concert_list');
    }
}