<?php

namespace App\Entity;

use App\Constant\Language;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/** @ORM\Entity(repositoryClass=UserRepository::class) @ORM\HasLifecycleCallbacks() */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /** @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue            */ private int     $id;
    /** @ORM\Column(type="string", length=180)                             */ private string  $name;
    /** @ORM\Column(type="string", length=180, unique=true, nullable=true) */ private ?string $email = null;
    /** @ORM\Column(type="string", length= 22, unique=true, nullable=true) */ private string  $code;
    /** @ORM\Column(type="string", length=  5                            ) */ private string  $lang = Language::ENGLISH;
    /** @ORM\Column(type="json")                                           */ private array   $roles = [];
    /** @ORM\Column(type="string",                          nullable=true) */ private ?string $password = null;
    /** @ORM\Column(type="datetime_immutable",                           ) */ private DateTimeImmutable $created;

    public function getId            (): ?int              { return $this->id      ; }
    public function getName          (): ?string           { return $this->name    ; }
    public function getEmail         (): ?string           { return $this->email   ; }
    public function getCode          (): ?string           { return $this->code    ; }
    public function getLang          (): string            { return $this->lang    ; }
    public function getPassword      (): ?string           { return $this->password; }
    public function getCreated       (): DateTimeImmutable { return $this->created ; }
    public function getSalt          (): ?string           { return null           ; }
    public function getRoles         (): array             { return $this->roles   ; }
    public function getUserIdentifier(): string            { return $this->id      ; }

    public function setName    (string $name    ): self { $this->name     = $name    ; return $this; }
    public function setEmail   (string $email   ): self { $this->email    = $email   ; return $this; }
    public function setCode    (string $code    ): self { $this->code     = $code    ; return $this; }
    public function setLang    (string $lang    ): self { $this->lang     = $lang    ; return $this; }
    public function setRoles   (array  $roles   ): self { $this->roles    = $roles   ; return $this; }
    public function setPassword(string $password): self { $this->password = $password; return $this; }
    /** @ORM\PrePersist() */ function prePersist()  { $this->created = new DateTimeImmutable(); }

    /** @deprecated since Symfony 5.3, use getUserIdentifier instead */
    public function getUsername(): string { return $this->getUserIdentifier(); }
    public function eraseCredentials(): void { }
}
