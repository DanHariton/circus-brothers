<?php

namespace App\Repository;

use App\Entity\Merch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Merch>
 *
 * @method Merch|null find($id, $lockMode = null, $lockVersion = null)
 * @method Merch|null findOneBy(array $criteria, array $orderBy = null)
 * @method Merch[]    findAll()
 * @method Merch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MerchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Merch::class);
    }

    /**
     * @return Merch[]
     */
    public function findWithLimitCount(int $count, int $id = null): array
    {
        $query = $this->createQueryBuilder('m')
            ->andWhere('m.active = :active')
            ->setParameter('active', Merch::STATUS_ACTIVE);

        if (!is_null($id)) {
            $query
                ->andWhere('m.id != :id')
                ->setParameter('id', $id);
        }

        return $query
            ->orderBy('m.id', 'DESC')
            ->setMaxResults($count)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Merch[]
     */
    public function findActiveMerch(): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.active = :active')
            ->setParameter('active', Merch::STATUS_ACTIVE)
            ->orderBy('m.id', 'desc')
            ->getQuery()
            ->getResult();
    }
}
