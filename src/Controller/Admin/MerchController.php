<?php

namespace App\Controller\Admin;

use App\Entity\File;
use App\Entity\MediaContent;
use App\Entity\Merch;
use App\Form\Merch\MerchFormType;
use App\Repository\MerchRepository;
use App\Repository\SizeRepository;
use App\Service\Image\ImageUploader;
use App\Service\Merch\MerchService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route("/merch", name: "merch_")]
class MerchController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ImageUploader $imageUploader,
        private readonly TranslatorInterface $translator,
        private readonly SizeRepository $sizeRepository,
        private readonly MerchService $merchService,
    )
    {}

    #[Route("/", name: "list")]
    public function list(MerchRepository $merchRepository): Response
    {
        return $this->render('admin/actions/merch/list.html.twig', [
            'merchs' => $merchRepository->findAll()
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route("/create", name: "create")]
    public function create(Request $request): RedirectResponse|Response
    {
        $merch = new Merch();
        $form = $this->createForm(MerchFormType::class, $merch, [
            'selected_sizes' => $merch->getSizes()->toArray(),
            'all_sizes' => $this->sizeRepository->findAll()
        ])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Merch $merch */
            $merch = $form->getData();
            $merch->setPosition($this->merchService->getLastPosition());

            $images = $form->get('images')->getData();
            if (!empty($images)) {
                foreach ($images as $position => $image) {
                    $imageName = $this->imageUploader->upload($image, ImageUploader::TYPE_670_520);
                    $imageFile = new File();
                    $imageFile->setFileName($imageName);
                    $imageFile->setPosition($position);
                    $this->em->persist($imageFile);
                    $merch->addPhoto($imageFile);
                }
            }

            $this->em->persist($merch);
            $this->em->flush();

            $this->addFlash('success', $this->translator->trans('flash.saved'));
            return $this->redirectToRoute('merch_list');
        }

        return $this->render('admin/actions/merch/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route("/edit/{merch}", name: "edit")]
    public function edit(Merch $merch, Request $request): RedirectResponse|Response
    {
        $form = $this->createForm(MerchFormType::class, $merch, [
            'selected_sizes' => $merch->getSizes()->toArray(),
            'all_sizes' => $this->sizeRepository->findAll()
        ])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Merch $merch */
            $merch = $form->getData();
            $maxPosition = $merch->getMaxPosition();

            $images = $form->get('images')->getData();
            if (!empty($images)) {
                foreach ($images as $position => $image) {
                    $imageName = $this->imageUploader->upload($image, ImageUploader::TYPE_670_520);
                    $imageFile = new File();
                    $imageFile->setFileName($imageName);
                    $imageFile->setPosition($maxPosition + $position);
                    $this->em->persist($imageFile);
                    $merch->addPhoto($imageFile);
                }
            }

            $this->em->persist($merch);
            $this->em->flush();
            $this->addFlash('success', $this->translator->trans('flash.saved'));

            return $this->redirectToRoute('merch_list');
        }

        return $this->render('admin/actions/merch/edit.html.twig', [
            'merch' => $merch,
            'form' => $form->createView()
        ]);
    }

    #[Route("/photo/reorder/{merch}/{file}/{way}", name: "photo_reposition")]
    public function imageReposition(Merch $merch, File $file, string $way): RedirectResponse
    {
        $merch->reposition($file, $way === 'top' ? 1 : -1);

        $this->em->flush();
        $this->addFlash('success', $this->translator->trans('flash.saved'));

        return $this->redirectToRoute('merch_edit', ['merch' => $merch->getId()]);
    }

    #[Route("/reposition/{merch}/{way}", name: "reposition", requirements: ['direction' => 'up|down'])]
    public function reposition(Merch $merch, string $way): RedirectResponse
    {
        $this->merchService->reposition($merch, $way === 'up' ? -1 : 1);

        return $this->redirectToRoute('merch_list');
    }

    #[Route("/delete-photo/{merch}/{file}", name: "delete_photo")]
    public function deletePhoto(Merch $merch, File $file): RedirectResponse
    {
        $this->imageUploader->remove($file->getFileName());

        $this->em->remove($file);
        $this->em->flush();

        $this->addFlash('success', $this->translator->trans('flash.deleted'));

        return $this->redirectToRoute('merch_edit', ['merch' => $merch->getId()]);
    }

    #[Route("/toggle-status/{merch}", name: "toggle_status")]
    public function toggleStatus(Merch $merch): RedirectResponse
    {
        $merch->setActive(!$merch->isActive());

        if($merch->isActive()) {
            $merch->setPosition($this->merchService->getLastPosition());
        } else {
            $merch->setPosition(0);
        }

        $this->em->persist($merch);
        $this->em->flush();

        $this->addFlash('success', $this->translator->trans('flash.saved'));

        return $this->redirectToRoute('merch_list');
    }

    #[Route("/delete/{merch}", name: "delete")]
    public function delete(Merch $merch): RedirectResponse
    {
        $this->em->remove($merch);
        $this->em->flush();

        $this->addFlash('success', $this->translator->trans('flash.deleted'));

        return $this->redirectToRoute('merch_list');
    }
}