<?php

namespace App\Entity;

use App\Repository\TestRunRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity(repositoryClass=TestRunRepository::class) */
class TestRun
{
    /** @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer"  ) */ private  int        $id;
    /** @ORM\OneToMany(targetEntity=User::class, mappedBy="user") */ private  User       $user;
    /** @ORM\Column(type="datetime"                             ) */ private  DateTime   $started;
    /** @ORM\Column(type="datetime", nullable=true              ) */ private ?DateTime   $completed;

    function getId       ():  int        { return $this->id       ; }
    function getUser     ():  User       { return $this->user     ; }
    function getStarted  ():  DateTime   { return $this->started  ; }
    function getCompleted(): ?DateTime   { return $this->completed; }

    function setUser     ( User     $user     ): self { $this->user      = $user     ; return $this; }
    function setStarted  ( DateTime $started  ): self { $this->started   = $started  ; return $this; }
    function setCompleted(?DateTime $completed): self { $this->completed = $completed; return $this; }
}
