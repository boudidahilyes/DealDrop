<?php

namespace App\Repository;

use App\Entity\ProductForRent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductForRent>
 *
 * @method ProductForRent|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductForRent|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductForRent[]    findAll()
 * @method ProductForRent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductForRentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductForRent::class);
    }

//    /**
//     * @return ProductForRent[] Returns an array of ProductForRent objects
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
public function findAllProductForRent($value)
{
    return $this->createQueryBuilder('p')
    ->where('p.owner!=:id')
    ->andWhere('p.status = :status' )
    ->setParameter('id',$value)
    ->setParameter('status','Approved')
    ->getQuery()
    ->getResult();
}
public function setAvailabilityUnavailable($id)
{
    $em=$this->getEntityManager();
    $query=$em->createQuery("UPDATE APP\Entity\ProductForRent p
    SET p.disponibility = 'UnAvailable'
    WHERE p.id=:id ")
    ->setParameter('id',$id);
    return $query->getResult();
}
public function findAllProductForRentProfil($id)
{
    return $this->createQueryBuilder('p')
    ->where('p.owner=:id')
    ->andWhere('p.status!=:status')
    ->andWhere('p.status!=:status2')
    ->setParameter('id',$id)
    ->setParameter('status','Removed')
    ->setParameter('status2','Declined')
    ->getQuery()
    ->getResult();
}

//    public function findOneBySomeField($value): ?ProductForRent
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
