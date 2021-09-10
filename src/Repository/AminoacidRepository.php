<?php

namespace App\Repository;

use App\Entity\Aminoacid;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Aminoacid|null find($id, $lockMode = null, $lockVersion = null)
 * @method Aminoacid|null findOneBy(array $criteria, array $orderBy = null)
 * @method Aminoacid[]    findAll()
 * @method Aminoacid[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AminoacidRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Aminoacid::class);
    }

    // /**
    //  * @return Aminoacid[] Returns an array of Aminoacid objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Aminoacid
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
