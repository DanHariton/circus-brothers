<?php

namespace App\Controller\Admin;

use App\Entity\Merch;
use App\Repository\MerchRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/merch", name: "merch_")]
class MerchController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em)
    {}

    #[Route("/", name: "list")]
    public function list(MerchRepository $merchRepository)
    {
        return $this->render('admin/actions/merch/list.html.twig', [
            'merchs' => $merchRepository->findAll()
        ]);
    }

    #[Route("/create", name: "create")]
    public function create(Request $request)
    {

        return $this->render('admin/actions/merch/create.html.twig', [

        ]);
    }

    #[Route("/edit/{merch}", name: "edit")]
    public function edit(Merch $merch, Request $request)
    {

        return $this->render('admin/actions/merch/create.html.twig', [

        ]);
    }

    #[Route("/toggle-status/{merch}", name: "toggle_status")]
    public function toggleStatus(Merch $merch): RedirectResponse
    {
        $merch->setActive(!$merch->isActive());

        $this->em->persist($merch);
        $this->em->flush();

        return $this->redirectToRoute('merch_list');
    }

    #[Route("/delete/{merch}", name: "delete")]
    public function delete(Merch $merch): RedirectResponse
    {
        $this->em->remove($merch);
        $this->em->flush();

        return $this->redirectToRoute('merch_list');
    }
}