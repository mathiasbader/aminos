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
        $scores = [];
        foreach (GroupType::ALL as $groupType) {
            $qb = $this->createQueryBuilder('t');
            $qb->select('t');
            $qb->   where('t.user = :userId');
            $qb->andWhere('t.group = :groupType');
            $qb->setParameter('userId', $user->getId());
            $qb->setParameter('groupType', $groupType);
            $qb->orderBy('t.score', 'DESC');
            $qb->setMaxResults(1);

            /* @var $testRun TestRun */
            $testRun = $qb->getQuery()->getOneOrNullResult();
            if ($testRun === null) continue;

            $baseGroups = [];
            if ($testRun->getBaseScores() !== null &&
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
            }
            if ($onlyBasicScoresCombined) $baseGroups = GroupType::getBaseGroups($testRun->getGroup());
            foreach ($baseGroups as $group) {
                if ($testRun->getBaseScores() !== null &&
                    (!isset($scores[$group]) || $scores[$group][0] < $testRun->getScore())) {
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
