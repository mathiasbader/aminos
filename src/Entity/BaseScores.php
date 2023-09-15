<?php

namespace App\Entity;

use App\Repository\BaseScoresRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BaseScoresRepository::class)]
class BaseScores
{
    #[ORM\Id] #[ORM\GeneratedValue]
    #[ORM\Column]   private ?int     $id      ;
    #[ORM\OneToOne] private  TestRun $testRun ;
    #[ORM\Column]   private ?int     $unPolar1;
    #[ORM\Column]   private ?int     $unPolar2;
    #[ORM\Column]   private ?int     $polar   ;
    #[ORM\Column]   private ?int     $charged ;

    function __construct(?int $unPolar1, ?int $unPolar2, ?int $polar, ?int $charged) {
        $this->unPolar1 = $unPolar1;
        $this->unPolar2 = $unPolar2;
        $this->polar    = $polar   ;
        $this->charged  = $charged ;
    }

    function getId      (): ?int     { return $this->id      ; }
    function getTestRun (): ?TestRun { return $this->testRun ; }
    function getUnPolar1(): ?int     { return $this->unPolar1; }
    function getUnPolar2(): ?int     { return $this->unPolar2; }
    function getPolar   (): ?int     { return $this->polar   ; }
    function getCharged (): ?int     { return $this->charged ; }
}
