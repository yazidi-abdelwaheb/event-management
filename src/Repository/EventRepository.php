<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findAllMinContentPaginated(int $limit = 10, int $offset = 0): array
    {
        return $this->createQueryBuilder('e')
            ->select('e.id, e.title, e.image, e.start_date_time, e.end_date_time, e.location, e.price,e.capacity,e.status, c.label as category , COUNT(r.id) as subscribedCount')
            ->join('e.category', 'c')
            ->leftJoin('e.eventSubscribes', 'r')
            ->groupBy('e.id')
            ->orderBy('e.created_at', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findOneMinContent(int $id): ?array
    {
        return $this->createQueryBuilder('e')
            ->select('e.id, e.title, e.image, e.start_date_time, e.end_date_time,e.capacity,e.status, e.location, e.price, c.label as category , COUNT(r.id) as subscribedCount')
            ->join('e.category', 'c')
            ->leftJoin('e.eventSubscribes', 'r')
            ->where('e.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

     public function findAllForCalendar(): array
    {
        return $this->createQueryBuilder('e')
            ->select('e.id, e.title, e.description, e.image,e.capacity, e.start_date_time as startDateTime,e.status, e.end_date_time as endDateTime, e.location, e.price, c.label as category')
            ->join('e.category', 'c')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Event[] Returns an array of Event objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Event
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
