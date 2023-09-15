<?php

namespace App\Repository;

use App\Entity\BaseScores;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BaseScores>
 *
 * @method BaseScores|null find($id, $lockMode = null, $lockVersion = null)
 * @method BaseScores|null findOneBy(array $criteria, array $orderBy = null)
 * @method BaseScores[]    findAll()
 * @method BaseScores[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BaseScoresRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BaseScores::class);
    }
}
