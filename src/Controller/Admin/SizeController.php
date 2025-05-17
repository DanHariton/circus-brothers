<?php

namespace App\Controller\Admin;

use App\Entity\Size;
use App\Form\Size\SizeFormType;
use App\Repository\SizeRepository;
use App\Service\Size\SizeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/size", name: "size_")]
class SizeController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em, private readonly SizeService $sizeService)
    {
    }

    #[Route("/", name: "list")]
    public function list(SizeRepository $sizeRepository): Response
    {
        return $this->render('admin/actions/size/list.html.twig', [
            'sizes' => $sizeRepository->findAllOrderedByPosition()
        ]);
    }

    #[Route("/create", name: "create")]
    public function create(Request $request): RedirectResponse|Response
    {
        $size = new Size();
        $form = $this->createForm(SizeFormType::class, $size)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Size $size */
            $size = $form->getData();
            $size->setPosition($this->sizeService->getLastPosition());

            $this->em->persist($size);
            $this->em->flush();

            return $this->redirectToRoute("size_list");
        }

        return $this->render('admin/actions/size/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route("/edit/{size}", name: "edit")]
    public function edit(Size $size, Request $request): RedirectResponse|Response
    {
        $form = $this->createForm(SizeFormType::class, $size)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Size $size */
            $size = $form->getData();

            $this->em->persist($size);
            $this->em->flush();

            return $this->redirectToRoute("size_list");
        }

        return $this->render('admin/actions/size/edit.html.twig', [
            'size' => $size,
            'form' => $form->createView()
        ]);
    }

    #[Route("/delete/{size}", name: "delete")]
    public function delete(Size $size): RedirectResponse
    {
        $this->em->remove($size);
        $this->em->flush();

        return $this->redirectToRoute("size_list");
    }

    #[Route("/reposition/{size}/{way}", name: "reposition", requirements: ['direction' => 'up|down'])]
    public function reposition(Size $size, string $way): RedirectResponse
    {
        $this->sizeService->reposition($size, $way === 'up' ? -1 : 1);

        return $this->redirectToRoute('size_list');
    }
}