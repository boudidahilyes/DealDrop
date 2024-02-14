<?php

namespace App\Repository;

use App\Entity\ProductForSale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductForSale>
 *
 * @method ProductForSale|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductForSale|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductForSale[]    findAll()
 * @method ProductForSale[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductForSaleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductForSale::class);
    }

//    /**
//     * @return ProductForSale[] Returns an array of ProductForSale objects
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


//    public function findOneBySomeField($value): ?ProductForSale
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
