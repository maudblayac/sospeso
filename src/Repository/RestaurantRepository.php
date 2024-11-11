<?php

namespace App\Repository;

use App\Entity\Restaurant;
use App\DTO\RestaurantSearchDto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

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

    /**
     * Recherche les restaurants en fonction des critères du DTO RestaurantSearchDto
     *
     * @param RestaurantSearchDto $searchDto
     * @return Restaurant[]
     */
    public function searchByCriteria(RestaurantSearchDto $searchDto): array
    {
        $qb = $this->createQueryBuilder('r');

        // Exclure les restaurants en pause
        $qb->andWhere('r.hasListing = :hasListing')
           ->andWhere('r.isPaused = :isPaused')
           ->setParameter('hasListing', true)
           ->setParameter('isPaused', false);

        $this->applyFilters($qb, $searchDto);

        return $qb->getQuery()->getResult();
    }

    /**
     * Applique les filtres de recherche à la requête
     *
     * @param QueryBuilder $qb
     * @param RestaurantSearchDto $searchDto
     */
    private function applyFilters(QueryBuilder $qb, RestaurantSearchDto $searchDto): void
    {
        if ($searchDto->getCategories()) {
            $qb->andWhere('r.categories = :categories')
               ->setParameter('categories', $searchDto->getCategories());
        }

        if ($searchDto->getCity()) {
            $qb->andWhere('r.city LIKE :city')
               ->setParameter('city', '%' . $searchDto->getCity() . '%');
        }

        if ($searchDto->getMinPrice() !== null) {
            $qb->andWhere('r.minPrice >= :minPrice')
               ->setParameter('minPrice', $searchDto->getMinPrice());
        }

        if ($searchDto->getMaxPrice() !== null) {
            $qb->andWhere('r.maxPrice <= :maxPrice')
               ->setParameter('maxPrice', $searchDto->getMaxPrice());
        }
    }

    /**
     * Récupère les restaurants publics actifs (non en pause)
     *
     * @return Restaurant[]
     */
    public function findPublicActiveRestaurants(): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.hasListing = :hasListing')
            ->andWhere('r.isPaused = :isPaused')
            ->setParameter('hasListing', true)
            ->setParameter('isPaused', false)
            ->getQuery()
            ->getResult();
    }
}
