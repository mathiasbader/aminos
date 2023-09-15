<?php

namespace App\Entity;

use App\Constant\TestLevel;
use App\Repository\TestRunRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TestRunRepository::class)] #[ORM\Table(name: 'test_runs')]
class TestRun
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column(type: 'integer')] private int $id;
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'runs')] #[ORM\JoinColumn(nullable: false)]
                                                                  private  User       $user       ;
    #[ORM\Column(name: 'testGroup', type: 'string', length: 255)] private  string     $group      ;
    #[ORM\Column(type: 'datetime')]                               private  DateTime   $started    ;
    #[ORM\Column(type: 'datetime', nullable: true)]               private ?DateTime   $completed  ;
    #[ORM\Column(type: 'integer', nullable: true)]                private ?int        $level      ;
    #[ORM\OneToMany(mappedBy: 'run', targetEntity: Test::class, cascade: ['persist'], orphanRemoval: true)]
                                                                  private ?Collection $tests      ;
    #[ORM\ManyToMany(targetEntity: Aminoacid::class)]             private ?Collection $aminos     ;
    #[ORM\Column(type: 'integer', nullable: true)]                private ?int        $score      ;
    #[ORM\Column(type: 'integer', nullable: true)]                private ?int        $scoreBefore;
    #[ORM\OneToOne(targetEntity: BaseScores::class, mappedBy: 'testRun')] private ?Collection $baseScores ;

    private ?int   $correctCount = null;
    private ?int $incorrectCount = null;

    function __construct() {
        $this->tests  = new ArrayCollection();
        $this->aminos = new ArrayCollection();
        $this->baseScores = new ArrayCollection();
        $this->started = new DateTime();
    }

    function getId         ():  int        { return $this->id         ; }
    function getUser       ():  User       { return $this->user       ; }
    function getGroup      ():  string     { return $this->group      ; }
    function getStarted    ():  DateTime   { return $this->started    ; }
    function getCompleted  (): ?DateTime   { return $this->completed  ; }
    function getLevel      (): ?int        { return $this->level      ; }
    function getTests      (): ?Collection { return $this->tests      ; }
    function getAminos     (): ?Collection { return $this->aminos     ; }
    function getScore      (): ?int        { return $this->score      ; }
    function getScoreBefore(): ?int        { return $this->scoreBefore; }
    function getBaseScores (): ?Collection { return $this->baseScores ; }
    function getLastCompletedTest(): ?Test {
        $lastTest = null;
        foreach($this->tests as $test) {
            /* @var $test Test */
            if (($test->getAnswered() !== null) &&
                ($lastTest == null || $lastTest->getAnswered() < $test->getAnswered())) {
                    $lastTest = $test;
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
        $this->correctCount = 0;
        $this->incorrectCount = 0;
        foreach ($this->tests as $test) {
            /* @var $test Test */
            if     ($test->getCorrect() === true ) $this->  correctCount++;
            elseif ($test->getCorrect() === false) $this->incorrectCount++;
        }
    }
    function hasAnswers(): bool {
        $this->calculateCorrectCount();
        return $this->correctCount > 0 || $this->incorrectCount > 0;
    }
    function isFinished(): bool {
        $this->calculateCorrectCount();
        return $this->correctCount + $this->incorrectCount === $this->tests->count();
    }

    function calculateLevel(): void {
        $levels = [];
        foreach ($this->tests as $test) {
            /* @var $test Test */
            if (!array_key_exists($test->getAmino()->getId(), $levels)) {
                if (!$test->getCorrect()) $levels[$test->getAmino()->getId()] = 1;
                else                      $levels[$test->getAmino()->getId()] = $test->getLevel();
            } else {
                if ($test->getCorrect() && $test->getLevel() > $levels[$test->getAmino()->getId()]) {
                    $levels[$test->getAmino()->getId()] = $test->getLevel();
                }
            }
        }
        $level = TestLevel::LEVEL_3_CODE_TO_NAME;
        foreach ($levels as $thisLevel) {
            if ($thisLevel < $level) $level = $thisLevel;
        }
        $this->level = $level;
    }

    /** Calculates the percentage of correct answers and sets it to the score variable */
    function calculateScore(): void {
        $this->calculateCorrectCount();
        if ($this->hasAnswers()) {
            $this->score = intdiv($this->correctCount * 100, $this->incorrectCount + $this->correctCount);
        }
    }
     function getPercentageCorrect(): float {
        $this->calculateCorrectCount();
        return round($this->correctCount / ($this->tests->count()) * 100, 2);
    }
    function getPercentageIncorrect(): float {
        $this->calculateCorrectCount();
        return round($this->incorrectCount / ($this->tests->count()) * 100, 2);
    }

    function setUser       ( User       $user       ): self { $this->user        = $user       ; return $this; }
    function setGroup      ( string     $group      ): self { $this->group       = $group      ; return $this; }
    function setStarted    ( DateTime   $started    ): self { $this->started     = $started    ; return $this; }
    function setCompleted  (?DateTime   $completed  ): self { $this->completed   = $completed  ; return $this; }
    function setLevel      (?int        $level      ): self { $this->level       = $level      ; return $this; }
    function setAminos     ( Collection $aminos     ): self { $this->aminos      = $aminos     ; return $this; }
    function setScore      (?int        $score      ): self { $this->score       = $score      ; return $this; }
    function setScoreBefore(?int        $scoreBefore): self { $this->scoreBefore = $scoreBefore; return $this; }
    function setBaseScores ( Collection $baseScores ): self { $this->baseScores  = $baseScores ; return $this; }
    public function addTest(Test $test): self
    {
        if (!$this->tests->contains($test)) {
            $this->tests[] = $test;
            $test->setRun($this);
        }
        return $this;
    }
}
