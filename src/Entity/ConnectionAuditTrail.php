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
    private $started_at;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $ended_at;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $user_agent;

    /**
     * @var string
     * @ORM\Column(type="string", length=55, unique=true)
     */
    private $device_session_token;

    /**
     * @var string
     * @ORM\Column(type="string", length=55)
     */
    private $ip;

    /**
     * @var Application
     * @ORM\ManyToOne(targetEntity="App\Entity\Application")
     * @ORM\JoinColumn(nullable=false)
     */
    private $application;

    /**
     * 1=logout, 2=timeout, 3=force
     *
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $reason;

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
    public function getStartedAt(): DateTime
    {
        return $this->started_at;
    }

    /**
     * @param DateTime $started_at
     */
    public function setStartedAt(DateTime $started_at): void
    {
        $this->started_at = $started_at;
    }

    /**
     * @return DateTime
     */
    public function getEndedAt(): DateTime
    {
        return $this->ended_at;
    }

    /**
     * @param DateTime $ended_at
     */
    public function setEndedAt(DateTime $ended_at): void
    {
        $this->ended_at = $ended_at;
    }

    /**
     * @return string
     */
    public function getUseragent(): string
    {
        return $this->user_agent;
    }

    /**
     * @param string $user_agent
     */
    public function setUseragent(string $user_agent): void
    {
        $this->user_agent = $user_agent;
    }

    /**
     * @return string
     */
    public function getDeviceSessionToken(): string
    {
        return $this->device_session_token;
    }

    /**
     * @param string $device_session_token
     */
    public function setDeviceSessionToken(string $device_session_token): void
    {
        $this->device_session_token = $device_session_token;
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
     * @return Application
     */
    public function getApplication(): Application
    {
        return $this->application;
    }

    /**
     * @param Application $application
     */
    public function setApplication(Application $application): void
    {
        $this->application = $application;
    }

    /**
     * @return int
     */
    public function getReason(): int
    {
        return $this->reason;
    }

    /**
     * @param int $reason
     */
    public function setReason(int $reason): void
    {
        $this->reason = $reason;
    }
}
