<?php

namespace App\Repository;

use App\Entity\LiveStream;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LiveStream>
 */
class LiveStreamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LiveStream::class);
    }

    /**
     * @return LiveStream[]
     */
    public function findLiveStreams(int $limit = 12): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.live = :live')
            ->setParameter('live', true)
            ->orderBy('l.viewers', 'DESC')
            ->addOrderBy('l.startedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
