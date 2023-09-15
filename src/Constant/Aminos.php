<?php

namespace App\Constant;

class Aminos
{
    const GLY = 'g';
    const ALA = 'a';
    const VAL = 'v';
    const LEU = 'l';
    const ILE = 'i';

    const MET = 'm';
    const PHE = 'f';
    const TRP = 'w';
    const PRO = 'p';

    const ASN = 'n';
    const GLN = 'q';
    const SER = 's';
    const THR = 't';
    const CYS = 'c';
    const TYR = 'y';

    const ASP = 'd';
    const GLU = 'e';
    const LYS = 'k';
    const ARG = 'r';
    const HIS = 'h';

    const NOT_POLAR_1 = [self::GLY, self::ALA, self::VAL, self::LEU, self::ILE];
    const NOT_POLAR_2 = [self::MET, self::PHE, self::TRP, self::PRO];
    const POLAR       = [self::ASN, self::GLN, self::SER, self::THR, self::CYS, self::TYR];
    const CHARGED     = [self::ASP, self::GLU, self::LYS, self::ARG, self::HIS];

    const NOT_POLAR = [
        self::GLY, self::ALA, self::VAL, self::LEU, self::ILE,
        self::MET, self::PHE, self::TRP, self::PRO];
    const POLAR_CHARGED = [
        self::ASN, self::GLN, self::SER, self::THR, self::CYS, self::TYR,
        self::ASP, self::GLU, self::LYS, self::ARG, self::HIS];
    const ALL = [
        self::GLY, self::ALA, self::VAL, self::LEU, self::ILE,
        self::MET, self::PHE, self::TRP, self::PRO,
        self::ASN, self::GLN, self::SER, self::THR, self::CYS, self::TYR,
        self::ASP, self::GLU, self::LYS, self::ARG, self::HIS];

    static function getAminosOfGroup(string $group): array {
        $aminos = [];
        if     ($group == GroupType::GROUP_NOT_POLAR_1  ) $aminos = Aminos::NOT_POLAR_1;
        elseif ($group == GroupType::GROUP_NOT_POLAR_2  ) $aminos = Aminos::NOT_POLAR_2;
        elseif ($group == GroupType::GROUP_NOT_POLAR    ) $aminos = Aminos::NOT_POLAR;
        elseif ($group == GroupType::GROUP_POLAR        ) $aminos = Aminos::POLAR;
        elseif ($group == GroupType::GROUP_CHARGED      ) $aminos = Aminos::CHARGED;
        elseif ($group == GroupType::GROUP_POLAR_CHARGED) $aminos = Aminos::POLAR_CHARGED;
        elseif ($group == GroupType::GROUP_ALL          ) $aminos = Aminos::ALL;
        return $aminos;
    }

    static function getBaseGroup(string $amino): string {
        $amino = strtolower($amino);
        $group = '';
        if     (in_array($amino, self::NOT_POLAR_1)) $group = GroupType::GROUP_NOT_POLAR_1;
        elseif (in_array($amino, self::NOT_POLAR_2)) $group = GroupType::GROUP_NOT_POLAR_2;
        elseif (in_array($amino, self::POLAR      )) $group = GroupType::GROUP_POLAR      ;
        elseif (in_array($amino, self::CHARGED    )) $group = GroupType::GROUP_CHARGED    ;
        return $group;
    }
}
