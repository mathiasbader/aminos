<?php

namespace App\Repository;

use App\Entity\TestRun;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TestRun|null find($id, $lockMode = null, $lockVersion = null)
 * @method TestRun|null findOneBy(array $criteria, array $orderBy = null)
 * @method TestRun[]    findAll()
 * @method TestRun[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TestRunRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TestRun::class);
    }

    function getLevelsForUser(User $user): array {
        $qb = $this->createQueryBuilder('t');
        $qb->select('t.group');
        $qb->addSelect('MAX(t.level) as level');
        $qb->where('t.user = :userId');
        $qb->setParameter('userId', $user->getId());
        $qb->groupBy('t.group');

        $results   = $qb->getQuery()->getResult();

        $levels = [];
        foreach ($results as $result) $levels[$result['group']] = $result['level'];
        return $levels;
    }

    // /**
    //  * @return TestRun[] Returns an array of TestRun objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TestRun
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
