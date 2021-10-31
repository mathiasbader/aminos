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
    /** @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer"                                          ) */ private  int        $id;
    /** @ORM\ManyToOne(targetEntity=User::class, inversedBy="runs") @ORM\JoinColumn(nullable=false      ) */ private  User       $user;
    /** @ORM\Column(type="datetime"                                                                     ) */ private  DateTime   $started;
    /** @ORM\Column(type="datetime", nullable=true                                                      ) */ private ?DateTime   $completed;
    /** @ORM\OneToMany(targetEntity=Test::class, mappedBy="run", orphanRemoval=true, cascade={"persist"}) */ private ?Collection $tests;
    /** @ORM\ManyToMany(targetEntity=Aminoacid::class                                                   ) */ private ?Collection $aminos;

    private ?int   $correctCount = null;
    private ?int $incorrectCount = null;

    public function __construct() {
        $this->tests  = new ArrayCollection();
        $this->aminos = new ArrayCollection();
    }
    /** @ORM\PrePersist() */ function prePersist()  { $this->started = new DateTime(); }

    function getId       ():  int        { return $this->id       ; }
    function getUser     ():  User       { return $this->user     ; }
    function getStarted  ():  DateTime   { return $this->started  ; }
    function getCompleted(): ?DateTime   { return $this->completed; }
    function getTests    (): ?Collection { return $this->tests    ; }
    function getAminos   (): ?Collection { return $this->aminos   ; }
    function getLastCompletedTest(): ?Test {
        $lastTest = null;
        foreach($this->tests as $test) {
            /* @var $test Test */
            if ($test->getAnswered() !== null) {
                if ($lastTest == null || $lastTest->getAnswered() < $test->getAnswered()) $lastTest = $test;
            }
        }
        return $lastTest;
    }
    function getFirstUncompletedTest(): ?Test {
        foreach($this->tests as $test) {
            /* @var $test Test */
            if ($test->getAnswered() === null) return $test;
        }
        return null;
    }
    function calculateCorrectCount(): void {
        if ($this->correctCount === null) {
            $this->  correctCount = 0;
            $this->incorrectCount = 0;
            foreach ($this->tests as $test) {
                /* @var $test Test */
                if     ($test->getCorrect() === true ) $this->  correctCount++;
                elseif ($test->getCorrect() === false) $this->incorrectCount++;
            }
        }
    }
    function getCorrectCount(): int {
        $this->calculateCorrectCount();
        return $this->correctCount;
    }
    function getIncorrectCount(): int {
        $this->calculateCorrectCount();
        return $this->incorrectCount;
    }
    function getPercentageCorrect(): float {
        $this->calculateCorrectCount();
        return round($this->correctCount / ($this->tests->count()) * 100, 2);
    }
    function getPercentageIncorrect(): float {
        $this->calculateCorrectCount();
        return round($this->incorrectCount / ($this->tests->count()) * 100, 2);
    }

    function setUser     ( User       $user     ): self { $this->user      = $user     ; return $this; }
    function setStarted  ( DateTime   $started  ): self { $this->started   = $started  ; return $this; }
    function setCompleted(?DateTime   $completed): self { $this->completed = $completed; return $this; }
    function setAminos   ( Collection $aminos   ): self { $this->aminos    = $aminos   ; return $this; }

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

    public function addAmino   (Aminoacid $amino): self { if (!$this->aminos->contains($amino)) $this->aminos[] = $amino; return $this; }
    public function removeAmino(Aminoacid $amino): self { $this->aminos->removeElement($amino);                           return $this; }
}
