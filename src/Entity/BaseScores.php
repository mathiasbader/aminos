<?php

namespace App\Entity;

use App\Repository\BaseScoresRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BaseScoresRepository::class)]
class BaseScores
{
    #[ORM\Id] #[ORM\GeneratedValue]
    #[ORM\Column]                 private ?int     $id       ;
    #[ORM\OneToOne]               private  TestRun $testRun  ;
    #[ORM\Column(nullable: true)] private ?int     $nonPolar1;
    #[ORM\Column(nullable: true)] private ?int     $nonPolar2;
    #[ORM\Column(nullable: true)] private ?int     $polar    ;
    #[ORM\Column(nullable: true)] private ?int     $charged  ;

    function __construct(TestRun $testRun, ?int $nonPolar1, ?int $nonPolar2, ?int $polar, ?int $charged) {
        $this->testRun   = $testRun  ;
        $this->nonPolar1 = $nonPolar1;
        $this->nonPolar2 = $nonPolar2;
        $this->polar     = $polar    ;
        $this->charged   = $charged  ;
    }

    function getId       (): ?int     { return $this->id       ; }
    function getTestRun  (): ?TestRun { return $this->testRun  ; }
    function getNonPolar1(): ?int     { return $this->nonPolar1; }
    function getNonPolar2(): ?int     { return $this->nonPolar2; }
    function getPolar    (): ?int     { return $this->polar    ; }
    function getCharged  (): ?int     { return $this->charged  ; }
}
