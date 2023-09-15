<?php

namespace App\Entity;

use App\Constant\Common;
use App\Repository\TestRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TestRepository::class)] #[ORM\Table(name: 'tests')]
class Test
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column(type: 'integer')] private int $id;
    #[ORM\ManyToOne(targetEntity: TestRun::class, inversedBy: 'run')]
    #[ORM\JoinColumn(nullable: false)]                             private TestRun $run;
    #[ORM\ManyToOne(targetEntity: Aminoacid::class, inversedBy: 'run')]
    #[ORM\JoinColumn(nullable: false)] private Aminoacid $amino;
    #[ORM\Column(type: 'integer', nullable: true)] private ?int $level;
    #[ORM\Column(type: 'boolean', nullable: true)] private ?bool $correct;
    #[ORM\Column(type: 'string', length: 255, nullable: true)] private ?string $answer;
    #[ORM\ManyToOne(targetEntity: Aminoacid::class, inversedBy: 'run')] #[ORM\JoinColumn(nullable: true)]
    private ?Aminoacid $answerAmino;
    #[ORM\Column(type: 'datetime', nullable: true)] private ?DateTime $answered;
     #[ORM\ManyToMany(targetEntity: Aminoacid::class)] private ?Collection $choices;

     public function __construct() { $this->choices = new ArrayCollection(); }

    function getId         ():  int        { return $this->id         ; }
    function getRun        ():  TestRun    { return $this->run        ; }
    function getAmino      ():  Aminoacid  { return $this->amino      ; }
    function getLevel      (): ?int        { return $this->level      ; }
    function getCorrect    (): ?bool       { return $this->correct    ; }
    function getAnswer     (): ?string     { return $this->answer     ; }
    function getAnswerAmino(): ?Aminoacid  { return $this->answerAmino; }
    function getAnswered   (): ?DateTime   { return $this->answered   ; }
    function getChoices    ():  Collection {
        $choicesArray = $this->choices->toArray();
        shuffle($choicesArray);
        return new ArrayCollection($choicesArray);
    }

    function setRun        (TestRun    $run        ): self { $this->run         = $run        ; return $this; }
    function setAmino      (Aminoacid  $amino      ): self { $this->amino       = $amino      ; return $this; }
    function setLevel      (int        $level      ): self { $this->level       = $level      ; return $this; }
    function setCorrect    (bool       $correct    ): self { $this->correct     = $correct    ; return $this; }
    function setAnswer     (string     $answer     ): self { $this->answer      = $answer     ; return $this; }
    function setAnswerAmino(Aminoacid  $answerAmino): self { $this->answerAmino = $answerAmino; return $this; }
    function setAnswered   (DateTime   $answered   ): self { $this->answered    = $answered   ; return $this; }
    function defineChoices (Collection $choices    ): self {
        if ($choices->count() === 0) return $this;

        // ensure max size
        $choicesArray = $choices->toArray();
        if (count($choicesArray) > Common::MAX_ANSWERS_COUNT_FOR_NAME_TO_IMAGE) {
            $choicesArray = array_slice($choicesArray, 0, Common::MAX_ANSWERS_COUNT_FOR_NAME_TO_IMAGE);
        }

        // ensure that correct answer is still in array
        $correctAnswerAvailable = false;
        foreach ($choicesArray as $amino) {
            if ($amino->getId() === $this->amino->getId()) {
                $correctAnswerAvailable = true;
                break;
            }
        }
        if (!$correctAnswerAvailable) {
            $choicesArray[rand(0, Common::MAX_ANSWERS_COUNT_FOR_NAME_TO_IMAGE - 1)] = $this->amino;
        }

        $choices = new ArrayCollection($choicesArray);
        $this->choices = $choices;
        return $this;
    }
}
