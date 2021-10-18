<?php

namespace App\Entity;

use App\Repository\TestRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TestRepository::class)
 */
class Test
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $run;

    /**
     * @ORM\Column(type="integer")
     */
    private $amino;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $correct;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $answer;

    // Todo: Add answered as datetime

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRun(): ?int
    {
        return $this->run;
    }

    public function setRun(int $run): self
    {
        $this->run = $run;

        return $this;
    }

    public function getAmino(): ?int
    {
        return $this->amino;
    }

    public function setAmino(int $amino): self
    {
        $this->amino = $amino;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCorrect(): ?bool
    {
        return $this->correct;
    }

    public function setCorrect(?bool $correct): self
    {
        $this->correct = $correct;

        return $this;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(?string $answer): self
    {
        $this->answer = $answer;

        return $this;
    }
}
