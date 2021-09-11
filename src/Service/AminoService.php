<?php

namespace App\Service;

use App\Constant\Common;
use App\Entity\Aminoacid;

class AminoService
{
    function getOtherAminoIds($aminoId, $count = 5): array {
        $ids = [];
        if ($count < 0 or $count >= Common::AMINOS_COUNT) return $ids;
        while (count($ids) < $count) {
            $newId = mt_rand(1, Common::AMINOS_COUNT);
            if ($newId !== $aminoId && !in_array($newId, $ids, true)) $ids[] = $newId;
        }
        return $ids;
    }

    function isCorrectAnswer(string $answer, Aminoacid $amino, bool $codeAlsoOk = true): bool {
        $answer = mb_strtolower($answer);
        $correct = $answer === mb_strtolower($amino->getNameEn()) ||
                   $answer === mb_strtolower($amino->getNameDe());
        if ($codeAlsoOk) {
            $correct = $correct ||
                       $answer === mb_strtolower($amino->getCode1 ()) ||
                       $answer === mb_strtolower($amino->getCode3 ());
        }
        return $correct;
    }
}