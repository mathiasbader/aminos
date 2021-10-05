<?php


namespace App\Service;


class UserService
{
    private static array $names = [
        'Robert', 'Patricia', 'John', 'Jennifer', 'Michael', 'Linda', 'Elizabeth', 'David', 'Barbara', 'Richard',
        'Susan', 'Joseph', 'Jessica', 'Thomas', 'Sarah', 'Charles', 'Nancy', 'Daniel', 'Lisa', 'Matthew', 'Betty',
        'Anthony', 'Margaret', 'Mark', 'Sandra', 'Donald', 'Ashley', 'Steven', 'Kimberly', 'Paul', 'Emily',
    ];

    function generateName(): string {
        return self::$names[rand(0, count(self::$names))];
    }

    function generateCode(int $length): string {
        return self::generateRandomString($length);
    }

    private static function generateRandomString(int $length): string {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
