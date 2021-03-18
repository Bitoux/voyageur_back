<?php

namespace App\Repository\LocationManagement;

use App\Entity\LocationManagement\Location;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Location|null find($id, $lockMode = null, $lockVersion = null)
 * @method Location|null findOneBy(array $criteria, array $orderBy = null)
 * @method Location[]    findAll()
 * @method Location[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }

    public function findClosest($lat, $long)
    {
        $qb = $this->createQueryBuilder('l')
            ->select('l')
            ->addSelect('((ACOS(SIN(:lat * PI() / 180) * SIN(l.latitude * PI() / 180) + COS(:lat * PI() / 180) * COS(l.latitude * PI() / 180) * COS((:lng - l.longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515 * 1.609344) as distance')
            ->where('l.active = 1')
            ->orderBy('distance')
            ->setParameter('lat', $lat)
            ->setParameter('lng', $long);

            return $qb->getQuery()->getResult();
    }

    // /**
    //  * @return Location[] Returns an array of Location objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Location
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
