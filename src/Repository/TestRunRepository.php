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

    function getScoresForUser(User $user, $onlyBasicScoresCombined = false): array {
        $qb = $this->createQueryBuilder('t');
        $qb->select('t.group');
        $qb->addSelect('MAX(t.score) as score');
        $qb->where('t.user = :userId');
        $qb->setParameter('userId', $user->getId());
        $qb->groupBy('t.group');
        $results   = $qb->getQuery()->getResult();

        $scores = [];
        $baseGroups = [];
        foreach ($results as $result) {
            if ($onlyBasicScoresCombined) $baseGroups = GroupType::getBaseGroup($result['group']);
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

    function findHighestScore(string $group, User $user): int {
        $qb = $this->createQueryBuilder('t');
        $qb->select('t.score');
        $qb->addSelect('MAX(t.score) as score');
        $qb->   where('t.group = :group');
        $qb->andWhere('t.user = :userId');
        $qb->setParameter('group', $group);
        $qb->setParameter('userId', $user->getId());
        $qb->groupBy('t.group');
        $results = $qb->getQuery()->getResult();
        if (!empty($results)) {
            $result = $results[0]['score'];
            if ($result === null) $result = 0;
        }
        else $result = 0;
        return $result;
    }
}
