<?php

declare(strict_types=1);

namespace App\Service\Merch;

use App\Entity\Merch;
use App\Repository\MerchRepository;
use Doctrine\ORM\EntityManagerInterface;

class MerchService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly MerchRepository $merchRepository
    ) {
    }

    public function reposition(Merch $merch, int $way): void
    {
        $orderedMerch = $this->merchRepository->findActiveSortedByPosition();
        $currentPosition = $merch->getPosition();

        $targetPosition = $currentPosition + $way;

        if ($targetPosition < 1 || $targetPosition > count($orderedMerch)) {
            return;
        }

        foreach ($orderedMerch as $content) {
            if ($content->getPosition() === $targetPosition) {
                $content->setPosition($currentPosition);
            }
        }

        $merch->setPosition($targetPosition);

        foreach ($orderedMerch as $merchOrder) {
            $this->em->persist($merchOrder);
        }

        $this->em->flush();
    }

    public function getLastPosition(): int
    {
        return ($this->merchRepository->findLastPosition()?->getPosition() ?? 1) + 1;
    }
}