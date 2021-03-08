<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Application.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ApplicationRepository")
 */
class Application
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
     * @ORM\Column(type="string", length=55)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @var string
     * @ORM\Column(type="string", length=55)
     */
    private $img;

    /**
     * @var ApplicationType
     * @ORM\ManyToOne(targetEntity="App\Entity\ApplicationType")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(type="string", length=5)
     */
    private $version;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $api_token;

    /**
     * @var User[]
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="applications")
     */
    private $users;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getImg(): string
    {
        return $this->img;
    }

    public function setImg(string $img): void
    {
        $this->img = $img;
    }

    public function getType(): ApplicationType
    {
        return $this->type;
    }

    public function setType(ApplicationType $type): void
    {
        $this->type = $type;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    public function getApiToken(): string
    {
        return $this->api_token;
    }

    public function setApiToken(string $api_token): void
    {
        $this->api_token = $api_token;
    }
}
