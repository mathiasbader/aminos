<?php

namespace App\Entity;

use App\Repository\TestRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity(repositoryClass=TestRepository::class) */
class Test
{
    /** @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer"                                      ) */ private  int       $id;
    /** @ORM\ManyToOne(targetEntity=TestRun::class, inversedBy="run") @ORM\JoinColumn(nullable=false) */ private  TestRun   $run;
    /** @ORM\ManyToOne(targetEntity=Aminoacid::class) @ORM\JoinColumn(nullable=false                ) */ private  Aminoacid $amino;
    /** @ORM\Column(type="integer" ,             nullable=true                                      ) */ private ?int       $type;
    /** @ORM\Column(type="boolean" ,             nullable=true                                      ) */ private ?bool      $correct;
    /** @ORM\Column(type="string"  , length=255, nullable=true                                      ) */ private ?string    $answer;
    /** @ORM\Column(type="datetime",             nullable=true                                      ) */ private ?DateTime  $answered;

    function getId      ():  int       { return $this->id      ; }
    function getRun     ():  TestRun   { return $this->run     ; }
    function getAmino   ():  Aminoacid { return $this->amino   ; }
    function getType    (): ?int       { return $this->type    ; }
    function getCorrect (): ?bool      { return $this->correct ; }
    function getAnswer  (): ?string    { return $this->answer  ; }
    function getAnswered(): ?DateTime  { return $this->answered; }

    function setRun     (TestRun   $run     ): self { $this->run      = $run     ; return $this; }
    function setAmino   (Aminoacid $amino   ): self { $this->amino    = $amino   ; return $this; }
    function setType    (int       $type    ): self { $this->type     = $type    ; return $this; }
    function setCorrect (bool      $correct ): self { $this->correct  = $correct ; return $this; }
    function setAnswer  (string    $answer  ): self { $this->answer   = $answer  ; return $this; }
    function setAnswered(DateTime  $answered): self { $this->answered = $answered; return $this; }
}
