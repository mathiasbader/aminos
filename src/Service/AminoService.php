<?php

namespace App\Service;

use App\Constant\Common;

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
}