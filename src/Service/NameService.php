<?php


namespace App\Service;


class NameService
{
    function getName(): string {
        $names = ['Lisa', 'Peter', 'Andrea', 'Laura', 'Viktor', 'Lena', 'Friedrich'];
        return $names[random_int(0, count($names) - 1)];
    }
}
