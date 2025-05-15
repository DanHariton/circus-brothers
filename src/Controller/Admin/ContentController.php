<?php

namespace App\Controller\Admin;

use App\Entity\File;
use App\Entity\MediaContent;
use App\Form\MediaContent\MediaContentFormType;
use App\Repository\MediaContentRepository;
use App\Service\Image\ImageUploader;
use App\Service\MediaContent\MediaContentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route("/content", name: "media_content_")]
class ContentController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ImageUploader $imageUploader,
        private readonly MediaContentRepository $contentRepository,
        private readonly TranslatorInterface $translator,
        private readonly MediaContentService $contentService,
    )
    {
    }

    #[Route("/", name: "list")]
    public function list(): Response
    {
        $contentItems = $this->contentRepository->findBy([], ['position' => 'asc']);

        return $this->render('admin/actions/mediaContent/list.html.twig', [
            'contentItems' => $contentItems
        ]);
    }

    #[Route("/create", name: "create")]
    public function create(Request $request): RedirectResponse|Response
    {
        $mediaContent = new MediaContent();

        $form = $this->createForm(MediaContentFormType::class, $mediaContent)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var MediaContent $mediaContent */
            $mediaContent = $form->getData();

            $mediaContentFile = $form->get('image')->getData();

            $position = ($this->contentRepository->findLastPosition()?->getPosition() ?? 1) + 1;
            $mediaContent->setPosition($position);

            if ($mediaContentFile) {
                $newFilename = $this->imageUploader->upload($mediaContentFile, ImageUploader::TYPE_1920x1080);
                $imageFile = new File();
                $imageFile->setFileName($newFilename);
                $this->em->persist($imageFile);
                $mediaContent->setPhoto($imageFile);
            }

            $this->em->persist($mediaContent);
            $this->em->flush();

            $this->addFlash('success', $this->translator->trans('flash.saved'));
            return $this->redirectToRoute('media_content_edit', ['mediaContent' => $mediaContent->getId()]);
        }

        return $this->render('admin/actions/mediaContent/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route("/edit/{mediaContent}", name: "edit")]
    public function edit(MediaContent $mediaContent, Request $request): RedirectResponse|Response
    {
        $type = (bool)$mediaContent->getPhoto() ? MediaContent::PHOTO : MediaContent::VIDEO;
        $form = $this->createForm(MediaContentFormType::class, $mediaContent, [
            'photo_content' => $type
        ])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var MediaContent $mediaContent */
            $mediaContent = $form->getData();

            if ($type == MediaContent::PHOTO) {
                $mediaContentFile = $form->get('image')->getData();
                if ($mediaContentFile) {
                    $newFilename = $this->imageUploader->upload($mediaContentFile, ImageUploader::TYPE_1920x1080);
                    $imageFile = new File();
                    $imageFile->setFileName($newFilename);
                    $this->em->persist($imageFile);
                    $mediaContent->setPhoto($imageFile);
                }
            }

            $this->em->persist($mediaContent);
            $this->em->flush();

            $this->addFlash('success', $this->translator->trans('flash.saved'));
            return $this->redirectToRoute('media_content_list');
        }

        return $this->render('admin/actions/mediaContent/edit.html.twig', [
            'mediaContentItem' => $mediaContent,
            'form' => $form->createView()
        ]);
    }

    #[Route("/delete/{mediaContent}", name: "delete")]
    public function delete(MediaContent $mediaContent): RedirectResponse
    {
        if ($mediaContent->getPhoto()) {
            $this->imageUploader->remove($mediaContent->getPhoto()->getFileName());
        }

        $this->em->remove($mediaContent);
        $this->em->flush();

        $this->addFlash('success', $this->translator->trans('flash.deleted'));

        return $this->redirectToRoute('media_content_list');
    }

    #[Route("/reposition/{mediaContent}/{way}", name: "reposition", requirements: ['direction' => 'up|down'])]
    public function reposition(MediaContent $mediaContent, string $way): RedirectResponse
    {
        $this->contentService->reposition($mediaContent, $way === 'up' ? -1 : 1);

        return $this->redirectToRoute('media_content_list');
    }

    #[Route("/toggle-status/{mediaContent}", name: "toggle_status")]
    public function toggleStatus(MediaContent $mediaContent): RedirectResponse
    {
        $mediaContent->setActive(!$mediaContent->isActive());

        $this->em->persist($mediaContent);
        $this->em->flush();

        $this->addFlash('success', $this->translator->trans('flash.saved'));

        return $this->redirectToRoute('media_content_list');
    }
}