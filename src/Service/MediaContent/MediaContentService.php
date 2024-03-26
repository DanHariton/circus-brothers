<?php

namespace App\Service\MediaContent;

class MediaContentService
{
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

        $sortedArray = [];
        $currentBlock = 1;
        $photoIndex = 1;
        $videoIndex = 1;
        while (!empty($photos) || !empty($videos)) {
            if ($photoIndex <= 2 && !empty($photos)) {
                $sortedArray[$currentBlock][] = array_shift($photos);
                $photoIndex++;
            } elseif ($videoIndex <= 1 && !empty($videos)) {
                if ($photoIndex > 2) {
                    $currentBlock++;
                    $photoIndex = 1;
                    $videoIndex = 1;
                }
                $sortedArray[$currentBlock][] = array_shift($videos);
                $videoIndex++;
            } else {
                $currentBlock++;
                $photoIndex = 1;
                $videoIndex = 1;
            }
        }
        return $sortedArray;
    }
}