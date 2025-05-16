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
        $merchList = array_values($orderedMerch);
        $currentIndex = array_search($merch, $merchList, true);

        if ($currentIndex === false) {
            return;
        }

        $targetIndex = $currentIndex + $way;

        if ($targetIndex < 0 || $targetIndex >= count($merchList)) {
            return;
        }

        $tmp = $merchList[$currentIndex];
        $merchList[$currentIndex] = $merchList[$targetIndex];
        $merchList[$targetIndex] = $tmp;

        foreach ($merchList as $index => $item) {
            $item->setPosition($index + 1);
            $this->em->persist($item);
        }

        $this->em->flush();
    }

    public function getLastPosition(): int
    {
        return ($this->merchRepository->findLastPosition()?->getPosition() ?? 1) + 1;
    }
}