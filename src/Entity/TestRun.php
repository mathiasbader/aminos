<?php

namespace App\Entity;

use App\Repository\TestRunRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity(repositoryClass=TestRunRepository::class) @ORM\HasLifecycleCallbacks() @ORM\Table(name="test_runs") */
class TestRun
{
    /** @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer")                                           */ private  int        $id;
    /** @ORM\ManyToOne(targetEntity=User::class, inversedBy="runs") @ORM\JoinColumn(nullable=false)       */ private  User       $user;
    /** @ORM\Column(type="datetime")                                                                      */ private  DateTime   $started;
    /** @ORM\Column(type="datetime", nullable=true)                                                       */ private ?DateTime   $completed;
    /** @ORM\OneToMany(targetEntity=Test::class, mappedBy="run", orphanRemoval=true, cascade={"persist"}) */ private ?Collection $tests;

    public function __construct() { $this->tests = new ArrayCollection(); }
    /** @ORM\PrePersist() */ function prePersist()  { $this->started = new DateTime(); }

    function getId       ():  int        { return $this->id       ; }
    function getUser     ():  User       { return $this->user     ; }
    function getStarted  ():  DateTime   { return $this->started  ; }
    function getCompleted(): ?DateTime   { return $this->completed; }
    function getTests    (): ?Collection { return $this->tests    ; }
    function getFirstUncompletedTest(): ?Test {
        foreach($this->tests as $test) {
            /* @var $test Test */
            if ($test->getAnswered() === null) return $test;
        }
        return null;
    }

    function setUser     ( User     $user     ): self { $this->user      = $user     ; return $this; }
    function setStarted  ( DateTime $started  ): self { $this->started   = $started  ; return $this; }
    function setCompleted(?DateTime $completed): self { $this->completed = $completed; return $this; }

    public function addTest(Test $test): self
    {
        if (!$this->tests->contains($test)) {
            $this->tests[] = $test;
            $test->setRun($this);
        }
        return $this;
    }

    public function removeTest(Test $test): self
    {
        if ($this->tests->removeElement($test)) {
            if ($test->getRun() === $this) $test->setRun(null);
        }
        return $this;
    }
}
