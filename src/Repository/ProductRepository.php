<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Returns filtered products based on name, type, price range, and sorting.
     *
     * @param string $name
     * @param string $type
     * @param string $range
     * @param string $sortby
     * @return Product[]
     */
    public function getFilteredProducts($name, $type, $range, $sortby): QueryBuilder {
        $qb = $this->createQueryBuilder('p');

        if (!empty($name)) {
            $qb->andWhere('LOWER(p.name) LIKE LOWER(:name)')
               ->setParameter('name', '%' . strtolower($name) . '%');
        }

        switch ($range) {
            case 'all':
                break;
            case 'under_25':
                $qb->andWhere('(p.price - p.price * p.sale) < 25');
                break;
            case '25_to_50':
                $qb->andWhere('(p.price - p.price * p.sale) >= 25 AND (p.price - p.price * p.sale) < 50');
                break;
            case '50_to_100':
                $qb->andWhere('(p.price - p.price * p.sale) >= 50 AND (p.price - p.price * p.sale) < 100');
                break;
            case '100_to_200':
                $qb->andWhere('(p.price - p.price * p.sale) >= 100 AND (p.price - p.price * p.sale) < 200');
                break;
            case '200above':
                $qb->andWhere('(p.price - p.price * p.sale) >= 200');
                break;
            default:
                throw new \InvalidArgumentException('Invalid range value');
        }

        switch ($type) {
            case 'any':
                break;
            case 'guns':
                $qb->andWhere('p INSTANCE OF :type')
                   ->setParameter('type', 'gun');
                break;
            case 'ammo':
                $qb->andWhere('p INSTANCE OF :type')
                   ->setParameter('type', 'ammo');
                break;
            case 'melee':
                $qb->andWhere('p INSTANCE OF :type')
                   ->setParameter('type', 'melee');
                break;
            case 'accessories':
                $qb->andWhere('p INSTANCE OF :type')
                   ->setParameter('type', 'accessory');
                break;
            default:
                throw new \InvalidArgumentException('Invalid type value');
        }

        switch ($sortby) {
            case 'featured':
                break;
            case 'price_low_to_high':
                $qb->orderBy('p.price', 'ASC');
                break;
            case 'price_high_to_low':
                $qb->orderBy('p.price', 'DESC');
                break;
            case 'customer_rating':
                $qb->orderBy('p.rating', 'DESC');
                break;
            case 'on_sale':
                $qb->andWhere('p.sale > 0.0')
                   ->orderBy('p.sale', 'DESC');
                break;
            default:
                throw new \InvalidArgumentException('Invalid sortby value');
        }
        
        return $qb;
    }

//    /**
//     * @return Product[] Returns an array of Product objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
