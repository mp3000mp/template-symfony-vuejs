<?php declare(strict_types=1);

namespace App\Service\Redis;

use Redis;

/**
 * Class RedisClient
 *
 * @package App\Service\Redis
 */
class RedisClient
{
    /** @var Redis  */
    private $client;

    /**
     * RedisClient constructor.
     *
     * @param string $host
     * @param int $port
     * @param string|null $auth
     */
    public function __construct(string $host = 'localhost', int $port = 6379, ?string $auth = null)
    {
        $this->client = new Redis();
        $this->client->connect($host, $port);

        if (null !== $auth) {
            $this->client->auth($auth);
        }
    }

    /**
     * @param string $key
     * @param mixed $isJson
     *
     * @return mixed
     */
    public function get(string $key, $isJson = true)
    {
        $r = $this->client->get($key);

        if (false !== $r && $isJson) {
            $r = json_decode($r, true);
        }
        return $r;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param int $timeout in seconds
     */
    public function set(string $key, $value, ?int $timeout = null): void
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }

        if (null === $timeout) {
            $this->client->set($key, $value);
        } else {
            $this->client->setex($key, $timeout, $value);
        }
    }

    /**
     * @return string|null
     */
    public function getLastError(): ?string
    {
        return $this->client->getLastError();
    }

    public function close(): void
    {
        $this->client->close();
    }

    /**
     * @param string $key
     */
    public function delete(string $key): void
    {
        $this->client->del($key);
    }
}
