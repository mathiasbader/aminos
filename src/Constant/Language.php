<?php


namespace App\Constant;

class Language
{
    const ENGLISH = 'en';
    const SPANISH = 'es';
    const GERMAN  = 'de';

    const DEFAULT = self::ENGLISH;

    static array $all = [self::ENGLISH, self::SPANISH, self::GERMAN];
}
