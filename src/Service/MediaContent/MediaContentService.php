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
        $contentList = array_values($orderedContents);

        $currentIndex = array_search($mediaContent, $contentList, true);

        if ($currentIndex === false) {
            return;
        }

        $targetIndex = $currentIndex + $way;

        if ($targetIndex < 0 || $targetIndex >= count($contentList)) {
            return;
        }

        $temp = $contentList[$currentIndex];
        $contentList[$currentIndex] = $contentList[$targetIndex];
        $contentList[$targetIndex] = $temp;

        foreach ($contentList as $index => $item) {
            $item->setPosition($index + 1);
            $this->em->persist($item);
        }

        $this->em->flush();
    }
}
