<?php

namespace App\Entity;

use App\Constant\Language;
use App\Constant\Representation;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/** @ORM\Entity(repositoryClass=UserRepository::class) @ORM\HasLifecycleCallbacks() @ORM\Table(name="users") */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /** @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue            */ private int               $id;
    /** @ORM\Column(type="string", length=180,              nullable=true) */ private string            $name;
    /** @ORM\Column(type="string", length=180, unique=true, nullable=true) */ private ?string           $email = null;
    /** @ORM\Column(type="string", length= 22, unique=true, nullable=true) */ private string            $code;
    /** @ORM\Column(type="string", length=  5                            ) */ private string            $lang = Language::ENGLISH;
    /** @ORM\Column(type="string", length=  5                            ) */ private string            $representation = Representation::SVG;
    /** @ORM\Column(type="json")                                           */ private array             $roles = [];
    /** @ORM\Column(type="string",                          nullable=true) */ private ?string           $password = null;
    /** @ORM\Column(type="datetime_immutable",                           ) */ private DateTimeImmutable $created;
    /** @ORM\OneToMany(targetEntity=TestRun::class, mappedBy="user"      ) */ private ?Collection       $runs;

    public function __construct() { $this->runs = new ArrayCollection(); }
    /** @ORM\PrePersist() */ function prePersist()  { $this->created = new DateTimeImmutable(); }

    public function getId            (): ?int              { return $this->id            ; }
    public function getName          (): ?string           { return $this->name          ; }
    public function getEmail         (): ?string           { return $this->email         ; }
    public function getCode          (): ?string           { return $this->code          ; }
    public function getLang          (): string            { return $this->lang          ; }
    public function getRepresentation(): string            { return $this->representation; }
    public function getPassword      (): ?string           { return $this->password      ; }
    public function getCreated       (): DateTimeImmutable { return $this->created       ; }
    public function getSalt          (): ?string           { return null                 ; }
    public function getRoles         (): array             { return $this->roles         ; }
    public function getUserIdentifier(): string            { return $this->id            ; }

    public function setName          (string $name          ): self { $this->name           = $name          ; return $this; }
    public function setEmail         (string $email         ): self { $this->email          = $email         ; return $this; }
    public function setCode          (string $code          ): self { $this->code           = $code          ; return $this; }
    public function setLang          (string $lang          ): self { $this->lang           = $lang          ; return $this; }
    public function setRepresentation(string $representation): self { $this->representation = $representation; return $this; }
    public function setRoles         (array  $roles         ): self { $this->roles          = $roles         ; return $this; }
    public function setPassword      (string $password      ): self { $this->password       = $password      ; return $this; }

    /** @deprecated since Symfony 5.3, use getUserIdentifier instead */
    public function getUsername(): string { return $this->getUserIdentifier(); }
    public function eraseCredentials(): void { }

    public function getRuns(): Collection { return $this->runs; }

    public function addRun(TestRun $run): self
    {
        if (!$this->runs->contains($run)) {
            $this->runs[] = $run;
            $run->setUser($this);
        }
        return $this;
    }

    public function removeRun(TestRun $run): self
    {
        if ($this->runs->removeElement($run)) {
            if ($run->getUser() === $this) { $run->setUser(null); }
        }
        return $this;
    }
}
