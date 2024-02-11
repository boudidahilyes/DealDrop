<?php

namespace App\Repository;

use App\Entity\DriverLicenseImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DriverLicenseImage>
 *
 * @method DriverLicenseImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method DriverLicenseImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method DriverLicenseImage[]    findAll()
 * @method DriverLicenseImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DriverLicenseImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DriverLicenseImage::class);
    }

//    /**
//     * @return DriverLicenseImage[] Returns an array of DriverLicenseImage objects
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

//    public function findOneBySomeField($value): ?DriverLicenseImage
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
