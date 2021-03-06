<?php


namespace App\Constant;

class GroupType
{
    const GROUP_NOT_POLAR_1   = 'notPolar1';
    const GROUP_NOT_POLAR_2   = 'notPolar2';
    const GROUP_NOT_POLAR     = 'notPolar';
    const GROUP_POLAR         = 'polar';
    const GROUP_CHARGED       = 'charged';
    const GROUP_POLAR_CHARGED = 'polarCharged';
    const GROUP_ALL           = 'all';

    const ALL = [
        self::GROUP_NOT_POLAR_1, self::GROUP_NOT_POLAR_2, self::GROUP_NOT_POLAR    ,
        self::GROUP_POLAR      , self::GROUP_CHARGED    , self::GROUP_POLAR_CHARGED,
        self::GROUP_ALL,
    ];
}
