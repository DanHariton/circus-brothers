<?php

namespace App\Service\MediaContent;

use App\Entity\MediaContent;
use App\Repository\MediaContentRepository;
use Doctrine\ORM\EntityManagerInterface;

class MediaContentService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly MediaContentRepository $mediaContentRepository
    ) {
    }

    public function sortMediaContentByPattern(array $mediaContents): array
    {
        $photos = [];
        $videos = [];

        foreach ($mediaContents as $mediaContent) {
            if (is_null($mediaContent->getVideoLink())) {
                $photos[] = $mediaContent;
            } else {
                $videos[] = $mediaContent;
            }
        }

        return [
            'photos' => $photos,
            'videos' => $videos
        ];
    }

    public function reposition(MediaContent $mediaContent, int $way): void
    {
        $orderedContents = $this->mediaContentRepository->findActiveContentSortedByPosition();
        $currentPosition = $mediaContent->getPosition();

        $targetPosition = $currentPosition + $way;

        if ($targetPosition < 1 || $targetPosition > count($orderedContents)) {
            return;
        }

        foreach ($orderedContents as $content) {
            if ($content->getPosition() === $targetPosition) {
                $content->setPosition($currentPosition);
            }
        }

        $mediaContent->setPosition($targetPosition);

        foreach ($orderedContents as $content) {
            $this->em->persist($content);
        }

        $this->em->flush();
    }
}
