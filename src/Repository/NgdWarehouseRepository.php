<?php

namespace App\Repository;

use App\Entity\NgdWarehouse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NgdWarehouse|null find($id, $lockMode = null, $lockVersion = null)
 * @method NgdWarehouse|null findOneBy(array $criteria, array $orderBy = null)
 * @method NgdWarehouse[]    findAll()
 * @method NgdWarehouse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NgdWarehouseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NgdWarehouse::class);
    }

    // /**
    //  * @return NgdWarehouse[] Returns an array of NgdWarehouse objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NgdWarehouse
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
