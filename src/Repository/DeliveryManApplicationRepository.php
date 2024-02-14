<?php

namespace App\Repository;

use App\Entity\DeliveryManApplication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DeliveryManApplication>
 *
 * @method DeliveryManApplication|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeliveryManApplication|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeliveryManApplication[]    findAll()
 * @method DeliveryManApplication[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeliveryManApplicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeliveryManApplication::class);
    }

//    /**
//     * @return DeliveryManApplication[] Returns an array of DeliveryManApplication objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DeliveryManApplication
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
