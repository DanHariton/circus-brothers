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

        return [
            'photos' => $photos,
            'videos' => $videos
        ];
    }
}