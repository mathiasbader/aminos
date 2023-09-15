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

#[ORM\Entity(repositoryClass: UserRepository::class)] #[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column(type: 'integer')]           private  int    $id;
    #[ORM\Column(type: 'string', length: 180,               nullable: true)] private  string $name;
    #[ORM\Column(type: 'string', length: 180, unique: true, nullable: true)] private ?string $email = null;
    #[ORM\Column(type: 'string', length:  22, unique: true, nullable: true)] private  string $code;
    #[ORM\Column(type: 'string', length:   5                              )] private  string $lang = Language::ENGLISH;
    #[ORM\Column(type: 'string', length:   5                              )] private  string $representation = Representation::SVG;
    #[ORM\Column(type: 'json'                                             )] private  array  $roles = [];
    #[ORM\Column(type: 'string',                            nullable: true)] private ?string $password = null;
    #[ORM\Column(type: 'datetime_immutable'                               )] private  DateTimeImmutable $created;
    #[ORM\OneToMany(targetEntity: TestRun::class, mappedBy: 'user'        )] private ?Collection $runs;

    public function __construct() {
        $this->runs = new ArrayCollection();
        $this->created = new DateTimeImmutable();
    }

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

    public function setName          (string $name          ): void { $this->name           = $name          ; }
    public function setEmail         (string $email         ): void { $this->email          = $email         ; }
    public function setCode          (string $code          ): void { $this->code           = $code          ; }
    public function setLang          (string $lang          ): void { $this->lang           = $lang          ; }
    public function setRepresentation(string $representation): void { $this->representation = $representation; }
    public function setRoles         (array  $roles         ): void { $this->roles          = $roles         ; }
    public function setPassword      (string $password      ): void { $this->password       = $password      ; }

    /** @deprecated since Symfony 5.3, use getUserIdentifier instead */
    public function getUsername(): string { return $this->getUserIdentifier(); }
    public function eraseCredentials(): void { }
}
