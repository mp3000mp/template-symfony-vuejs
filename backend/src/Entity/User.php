<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User.
 *
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="email", message="This email is not available")
 * @UniqueEntity(fields="username", message="This username is not available")
 */
class User implements \Serializable, UserInterface
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=100, unique=true)
     * @Assert\Email(message = "entity.User.constraint.email.email")
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(type="string", length=55, unique=true)
     * @Assert\Email(message = "entity.User.constraint.email.username")
     */
    private $username;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @var DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $password_updated_at;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $reset_password_token;

    /**
     * @var DateTime|null
     * @ORM\Column(type="datetime", length=255, nullable=true)
     */
    private $reset_password_at;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $isEnabled = false;

    /**
     * @var array
     * @ORM\Column(type="json", nullable=false)
     */
    private $roles = [];

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $isSuperAdmin = false;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPasswordUpdatedAt()
    {
        return $this->password_updated_at;
    }

    /**
     * @param mixed $password_updated_at
     */
    public function setPasswordUpdatedAt($password_updated_at): void
    {
        $this->password_updated_at = $password_updated_at;
    }

    /**
     * @return mixed
     */
    public function getResetPasswordToken()
    {
        return $this->reset_password_token;
    }

    /**
     * @param mixed $reset_password_token
     */
    public function setResetPasswordToken($reset_password_token): void
    {
        $this->reset_password_token = $reset_password_token;
    }

    /**
     * @return mixed
     */
    public function getResetPasswordAt()
    {
        return $this->reset_password_at;
    }

    /**
     * @param mixed $reset_password_at
     */
    public function setResetPasswordAt($reset_password_at): void
    {
        $this->reset_password_at = $reset_password_at;
    }

    public function getIsEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): void
    {
        $this->isEnabled = $isEnabled;
    }

    /**
     * Returns the roles or permissions granted to the user for security.
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        if (!in_array('ROLE_USER', $roles, true)) {
            $roles[] = 'ROLE_USER';
        }

        return $roles;
    }

    /**
     * @return User
     */
    public function setRoles(array $roles): self
    {
        if (!in_array('ROLE_USER', $roles, true)) {
            $roles[] = 'ROLE_USER';
        }
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return $this
     */
    public function addRole(string $role): self
    {
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        return serialize([
            $this->id,
            $this->username,
            $this->email,
            $this->password,
            $this->isEnabled,
            // see section on salt below
            // $this->salt,
        ]);
    }

    /**
     * @param string $serialized
     *                           {@inheritdoc}
     */
    public function unserialize($serialized): array
    {
        return list($this->id, $this->username, $this->email, $this->password, $this->isEnabled,
            // $this->salt
            ) = unserialize($serialized, ['allowed_classes' => false]);
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials(): void
    {
    }

    public function getIsSuperAdmin(): bool
    {
        return $this->isSuperAdmin;
    }

    public function setIsSuperAdmin(bool $isSuperAdmin): void
    {
        $this->isSuperAdmin = $isSuperAdmin;
    }
}
