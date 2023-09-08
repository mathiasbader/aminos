<?php


namespace App\Service;


use App\Constant\GroupType;

class TestsService
{
    function getRecommendedNextGroup(array $scores): string {
        foreach (GroupType::ALL as $group) {
            if (!array_key_exists($group, $scores) || $scores[$group] < 100) return $group;
        }
        if (count($scores) !== count(GroupType::ALL)) return GroupType::GROUP_NOT_POLAR_1;
        return '';
    }
}
