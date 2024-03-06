<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[UniqueEntity('email', message: 'Email déjà utilisé')]
#[UniqueEntity('username', message: "nom d'utilisateur déjà utilisé")]
#[ORM\Table('user')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 25, unique: true)]
    #[Assert\NotBlank(message: "Vous devez saisir un nom d'utilisateur.")]
    private ?string $username = null;

    #[ORM\Column(type: 'string', length: 64)]
    #[Assert\PasswordStrength(message:'Mot de passe trop simple à deviner')]
    private string $password;

    #[ORM\Column(type: 'string', length: 60, unique: true)]
    #[Assert\NotBlank(message: 'Vous devez saisir une adresse email.')]
    #[Assert\Email(message: "Le format de l'adresse n'est pas correcte.")]
    private ?string $email = null;

    /**
     * Undocumented variable
     *
     * @var array<int,string>
     */
    #[ORM\Column(length: 50)]
    private array $roles = ['ROLE_USER'];

    /**
     * tasks linked to user
     *
     * @var Collection<int,Task>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Task::class, cascade: ['remove'])]
    #[ORM\JoinColumn()]
    private Collection $tasks;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    // public function getSalt()
    // {
    //     return null;
    // }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * set Roles
     *
     * @param array<int,string> $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * get Roles
     *
     * @return array<int,string>
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    /**
     * Get the value of tasks
     * @return Collection<int,Task>
     */
    public function getTasks(): ?Collection
    {
        return $this->tasks;
    }
}
