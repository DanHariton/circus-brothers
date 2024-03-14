<?php

namespace App\Repository;

use App\Entity\Concert;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Concert>
 *
 * @method Concert|null find($id, $lockMode = null, $lockVersion = null)
 * @method Concert|null findOneBy(array $criteria, array $orderBy = null)
 * @method Concert[]    findAll()
 * @method Concert[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConcertRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Concert::class);
    }

    /**
     * @return Concert[]
     */
    public function findFutureConcerts(int $count = null): array
    {
        $query = $this->createQueryBuilder('c')
            ->andWhere('c.date >= :date')
            ->setParameter('date', (new DateTime())->format('Y-m-d H:i:s'))
            ->orderBy('c.date', 'ASC');

        if (!is_null($count)) {
            $query->setMaxResults($count);
        }

        return $query
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Concert[]
     */
    public function findPastConcerts(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.date < :date')
            ->setParameter('date', (new DateTime())->format('Y-m-d H:i:s'))
            ->orderBy('c.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
