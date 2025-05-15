<?php

declare(strict_types=1);

namespace App\Service\Size;

use App\Entity\Size;
use App\Repository\SizeRepository;
use Doctrine\ORM\EntityManagerInterface;

class SizeService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SizeRepository $sizeRepository
    ) {
    }

    public function reposition(Size $size, int $way): void
    {
        $currentPosition = $size->getPosition();
        $orderedSizes = $this->sizeRepository->getSortedByPosition();

        $targetPosition = $currentPosition + $way;

        if ($targetPosition < 1 || $targetPosition > count($orderedSizes)) {
            return;
        }

        foreach ($orderedSizes as $content) {
            if ($content->getPosition() === $targetPosition) {
                $content->setPosition($currentPosition);
            }
        }

        $size->setPosition($targetPosition);

        foreach ($orderedSizes as $merchOrder) {
            $this->em->persist($merchOrder);
        }

        $this->em->flush();
    }

    public function getLastPosition(): int
    {
        return ($this->sizeRepository->findLastPosition()?->getPosition() ?? 1) + 1;
    }
}