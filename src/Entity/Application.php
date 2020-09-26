<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Application
 *
 * @package App\Entity
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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getImg(): string
    {
        return $this->img;
    }

    /**
     * @param string $img
     */
    public function setImg(string $img): void
    {
        $this->img = $img;
    }

    /**
     * @return ApplicationType
     */
    public function getType(): ApplicationType
    {
        return $this->type;
    }

    /**
     * @param ApplicationType $type
     */
    public function setType(ApplicationType $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getApiToken(): string
    {
        return $this->api_token;
    }

    /**
     * @param string $api_token
     */
    public function setApiToken(string $api_token): void
    {
        $this->api_token = $api_token;
    }

}
