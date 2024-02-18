<?php

namespace App\Repository;

use App\Entity\ProductForTrade;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductForTrade>
 *
 * @method ProductForTrade|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductForTrade|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductForTrade[]    findAll()
 * @method ProductForTrade[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductForTradeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductForTrade::class);
    }

//    /**
//     * @return ProductForTrade[] Returns an array of ProductForTrade objects
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
public function findAllProductForSale($value)
{
    return $this->createQueryBuilder('p')
    ->where('p.owner!=:id')
    ->andWhere('p.status = :status' )
    ->setParameter('id',$value)
    ->setParameter('status','APPROVED')
    ->getQuery()
    ->getResult();
}
public function setStatusSold($id)
{
    $em=$this->getEntityManager();
    $query=$em->createQuery("UPDATE APP\Entity\ProductForTrade p
    SET p.status = 'SOLD'
    WHERE p.id=:id ")
    ->setParameter('id',$id);
    return $query->getResult();
}
//    public function findOneBySomeField($value): ?ProductForTrade
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
