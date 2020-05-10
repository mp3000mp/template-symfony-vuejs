<?php declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class ConnectionAuditTrail
 *
 * @package App\Entity
 *
 * @ORM\Entity(repositoryClass="App\Repository\ConnectionAuditTrailRepository")
 */
class ConnectionAuditTrail
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\user")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $device;

    /**
     * @var string
     * @ORM\Column(type="string", length=55)
     */
    private $ip;

    /**
     * @var string
     * @ORM\Column(type="string", length=55)
     */
    private $application;

    /**
     * 1=insert, 2=update, 3=show
     *
     * @var ApplicationType
     * @ORM\ManyToOne(targetEntity="App\Entity\ApplicationType")
     * @ORM\JoinColumn(nullable=false)
     */
    private $action;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updated_at;
    }

    /**
     * @param DateTime $updated_at
     */
    public function setUpdatedAt(DateTime $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    /**
     * @return string
     */
    public function getDevice(): string
    {
        return $this->device;
    }

    /**
     * @param string $device
     */
    public function setDevice(string $device): void
    {
        $this->device = $device;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    /**
     * @return string
     */
    public function getApplication(): string
    {
        return $this->application;
    }

    /**
     * @param string $application
     */
    public function setApplication(string $application): void
    {
        $this->application = $application;
    }

    /**
     * @return ApplicationType
     */
    public function getAction(): ApplicationType
    {
        return $this->action;
    }

    /**
     * @param ApplicationType $action
     */
    public function setAction(ApplicationType $action): void
    {
        $this->action = $action;
    }
}
