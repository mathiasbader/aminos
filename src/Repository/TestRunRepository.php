<?php

namespace App\Repository;

use App\Constant\GroupType;
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

    function getBasicScoresForUser(User $user): array {
        $qb = $this->createQueryBuilder('t');
        $qb->select('t.group');
        $qb->addSelect('MAX(t.score) as score');
        $qb->where('t.user = :userId');
        $qb->setParameter('userId', $user->getId());
        $qb->groupBy('t.group');

        $results   = $qb->getQuery()->getResult();

        $scores = [];
        foreach ($results as $result) {
            $baseGroups = GroupType::getBaseGroup($result['group']);
            if (empty($baseGroups)) {
                $scores[$result['group']] = $result['score'];
            } else {
                foreach($baseGroups as $group) {
                    if (!isset($scores[$group]) || $scores[$group] < $result['score']) {
                        $scores[$group] = $result['score'];
                    }
                }
            }
        }
        return $scores;
    }
}
