<?php


namespace App\Service;


use App\Constant\GroupType;

class TestsService
{
    function getRecommendedNextLevel(array $levels): string {
        foreach (GroupType::ALL as $group) {
            if (!array_key_exists($group, $levels) || $levels[$group] < 3) return $group;
        }
        if (count($levels) !== count(GroupType::ALL)) return GroupType::GROUP_NOT_POLAR_1;
        return '';
    }
}
