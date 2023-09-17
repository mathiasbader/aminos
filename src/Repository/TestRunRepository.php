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
        $qb->select('t');
        $qb->addSelect('MAX(t.score) AS HIDDEN score');
        $qb->where('t.user = :userId');
        $qb->setParameter('userId', $user->getId());
        $qb->groupBy('t.group');
        $results   = $qb->getQuery()->getResult();

        $scores = [];
        $baseGroups = [];
        foreach ($results as $testRun) {
            /* @var $testRun TestRun */
            if ($onlyBasicScoresCombined) $baseGroups = GroupType::getBaseGroups($testRun->getGroup());
            if (empty($baseGroups) &&
                (!isset($scores[$testRun->getGroup()]) || $scores[$testRun->getGroup()][0] < $testRun->getScore())) {
                $scores[$testRun->getGroup()] = [
                    $testRun->getScore(),
                    [
                        $testRun->getBaseScores()->getNonPolar1(),
                        $testRun->getBaseScores()->getNonPolar2(),
                        $testRun->getBaseScores()->getPolar(),
                        $testRun->getBaseScores()->getCharged(),
                    ]
                ];
            } else {
                foreach($baseGroups as $group) {
                    if (!isset($scores[$group]) || $scores[$group][0] < $testRun->getScore()) {
                        $scores[$group] = [
                            $testRun->getScore(),
                            [
                                $testRun->getBaseScores()->getNonPolar1(),
                                $testRun->getBaseScores()->getNonPolar2(),
                                $testRun->getBaseScores()->getPolar(),
                                $testRun->getBaseScores()->getCharged(),
                            ]
                        ];
                    }
                }
            }
        }
        return $scores;
    }

    function findHighestScore(string $group, User $user): ?TestRun {
        $qb = $this->createQueryBuilder('t');
        $qb->select('t');
        $qb->addSelect('t');
        $qb->   where('t.group = :group');
        $qb->andWhere('t.user = :userId');
        $qb->orderBy('t.score', 'DESC');
        $qb->setParameter('group', $group);
        $qb->setParameter('userId', $user->getId());
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
