<?php

namespace App\Entity;

use App\Repository\AminoacidRepository;
use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity(repositoryClass=AminoacidRepository::class) @ORM\Table(name="aminoacids") */
class Aminoacid
{
    /** @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue */ private int $id;
    /** @ORM\Column(type="string", length=255)                  */ private string $nameEn   ;
    /** @ORM\Column(type="string", length=255)                  */ private string $nameDe   ;
    /** @ORM\Column(type="string", length=255)                  */ private string $formula  ;
    /** @ORM\Column(type="smallint"          )                  */ private int    $essential;
    /** @ORM\Column(type="string", length=  1)                  */ private string $code1    ;
    /** @ORM\Column(type="string", length=  3)                  */ private string $code3    ;

    public function getId       (): ?int    { return $this->id       ; }
    public function getName     (): ?string { return $this->nameEn   ; }
    public function getNameEn   (): ?string { return $this->nameEn   ; }
    public function getNameDe   (): ?string { return $this->nameDe   ; }
    public function getFormula  (): ?string { return $this->formula  ; }
    public function getEssential(): int     { return $this->essential; }
    public function getCode1    (): ?string { return $this->code1    ; }
    public function getCode3    (): ?string { return $this->code3    ; }

    public function setNameEn   (string $nameEn   ): self { $this->nameEn    = $nameEn;    return $this; }
    public function setNameDe   (string $nameDe   ): self { $this->nameDe    = $nameDe;    return $this; }
    public function setFormula  (string $formula  ): self { $this->formula   = $formula;   return $this; }
    public function setEssential(int    $essential): self { $this->essential = $essential; return $this; }
    public function setCode1    (string $code1    ): self { $this->code1     = $code1;     return $this; }
    public function setCode3    (string $code3    ): self { $this->code3     = $code3;     return $this; }
}
