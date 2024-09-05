<?php

namespace App\Repository;

use App\Entity\Restaurant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Restaurant>
 */
class RestaurantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Restaurant::class);
    }
        /**
         * Récupère le prix minimum et maximum des produits d'un restaurant
         *
         * @param int $restaurantId
         * @return array
         */
        public function findMinMaxPriceByRestaurant(int $restaurantId): array
        {
            $qb = $this->createQueryBuilder('r')
                ->select('MIN(p.price) AS minPrice, MAX(p.price) AS maxPrice')
                ->innerJoin('r.products', 'p')
                ->where('r.id = :restaurantId')
                ->setParameter('restaurantId', $restaurantId)
                ->getQuery();
    
            return $qb->getSingleResult();
        }
    
}
    
    //    /**
    //     * @return Restaurant[] Returns an array of Restaurant objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Restaurant
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

