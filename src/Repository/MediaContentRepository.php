<?php

namespace App\Repository;

use App\Entity\MediaContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MediaContent>
 *
 * @method MediaContent|null find($id, $lockMode = null, $lockVersion = null)
 * @method MediaContent|null findOneBy(array $criteria, array $orderBy = null)
 * @method MediaContent[]    findAll()
 * @method MediaContent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediaContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MediaContent::class);
    }

    public function findLastPosition(): ?MediaContent
    {
        return $this->createQueryBuilder('m')
            ->orderBy('m.position', 'desc')
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
            ;
    }

    /**
     * @return MediaContent[]
     */
    public function findActiveContentSortedByPosition(): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.active = :active')
            ->setParameter('active', MediaContent::STATUS_ACTIVE)
            ->orderBy('m.position', 'asc')
            ->getQuery()
            ->getResult()
            ;
    }
}
