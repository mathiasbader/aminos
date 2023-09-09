<?php

namespace App\Entity;

use App\Constant\Language;
use App\Repository\AminoacidRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Yaml\Yaml;

/** @ORM\Entity(repositoryClass=AminoacidRepository::class) @ORM\Table(name="aminoacids") */
class Aminoacid
{
    /** @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue */ private int    $id       ;
    /** @ORM\Column(type="string", length=255)                  */ private string $name     ;
    /** @ORM\Column(type="string", length=255)                  */ private string $formula  ;
    /** @ORM\Column(type="smallint"          )                  */ private int    $essential;
    /** @ORM\Column(type="string", length=  1)                  */ private string $code1    ;
    /** @ORM\Column(type="string", length=  3)                  */ private string $code3    ;

    function getId       (): ?int    { return $this->id       ; }
    function getName     (): ?string { return $this->name     ; }
    function getFormula  (): ?string { return $this->formula  ; }
    function getEssential(): int     { return $this->essential; }
    function getCode1    (): ?string { return $this->code1    ; }
    function getCode3    (): ?string { return $this->code3    ; }
    function getImage($version = 'svg'): string
    {
        if ($version !== '3d') return '/img/aminos/svg/' . strtolower($this->code1) . '.svg';
        else                   return '/img/aminos/300/' . strtolower($this->code1) . '1.png';
    }
    function isCorrectAnswer(string $answer): bool {
        $name = $this->getName();
        if ($name === null) return false;
        $observed = mb_strtolower($answer);

        $correct = false;
        foreach (Language::$all as $lng) {
            $expected = mb_strtolower($this->getTranslation($name, $lng));
             $correct = $correct || trim($observed) === trim($expected);
        }
        return $correct;
    }

    function setName     (string $name     ): self { $this->name      = $name     ; return $this; }
    function setFormula  (string $formula  ): self { $this->formula   = $formula  ; return $this; }
    function setEssential(int    $essential): self { $this->essential = $essential; return $this; }
    function setCode1    (string $code1    ): self { $this->code1     = $code1    ; return $this; }
    function setCode3    (string $code3    ): self { $this->code3     = $code3    ; return $this; }

    function getTranslation(string $key, string $lng): string {
        $fileUrl = __DIR__ . "/../../translations/messages." . $lng . ".yaml";
        $yaml = Yaml::parse(file_get_contents($fileUrl));
        return $yaml['aminos'][$key];
    }
}
