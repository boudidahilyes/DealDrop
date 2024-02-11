<?php

namespace App\Repository;

use App\Entity\SupportTicketCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SupportTicketCategory>
 *
 * @method SupportTicketCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method SupportTicketCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method SupportTicketCategory[]    findAll()
 * @method SupportTicketCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SupportTicketCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SupportTicketCategory::class);
    }

//    /**
//     * @return SupportTicketCategory[] Returns an array of SupportTicketCategory objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SupportTicketCategory
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
