<?php declare(strict_types=1);

namespace App\Service\SharedSession;

use App\Entity\User;
use App\Service\Redis\RedisClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class SharedSession
 *
 * @package App\Service\SharedSession
 */
class SharedSession
{
    /** @var EntityManagerInterface */
    private $em;
    /** @var RedisClient */
    private $redis;
    /** @var SessionInterface */
    private $session;
    /** @var string  */
    private $prefix = '3S_';
    /** @var string|null */
    private $token;

    /**
     * SharedSession constructor.
     *
     * @param EntityManagerInterface $em
     * @param RedisClient $redis
     */
    public function __construct(EntityManagerInterface $em, RedisClient $redis, SessionInterface $session)
    {
        $this->em = $em;
        $this->redis = $redis;
        $this->session = $session;
    }

    /**
     * @param string $token
     *
     * @return bool
     */
    public function exists(string $token): bool
    {
        if (false !== $this->redis->get($token)) {
            $this->token = $token;
            return true;
        }
        return false;
    }

    /**
     * @param User $user
     *
     * @return string
     *
     * @throws \Exception
     */
    public function create(User $user): string
    {
        $this->generateToken();

        // set session token in db
        $user->setSharedSession($this->token);
        $this->em->persist($user);
        $this->em->flush();

        // set redis session
        $this->redis->set($user->getSharedSession(), $this->init($user));

        // set session
        $this->session->set('shared_session', $user->getSharedSession());

        return $this->token;
    }

    /**
     * @param User $user
     */
    public function destroy(User $user): void
    {
        if (null !== $user->getSharedSession()) {
            // destroy redis
            $this->redis->delete($user->getSharedSession());

            // destroy in session
            $this->session->remove($user->getSharedSession());

            // destroy in db
            $user->setSharedSession(null);
            $this->em->persist($user);
            $this->em->flush();
        }
    }

    /**
     * @param User $user
     *
     * @return array
     */
    private function init(User $user): array
    {
        return [
            'user_id' => $user->getId(),
            'token' => $user->getSharedSession(),
            'started_at' => date('U'),
            //'last_refresh_at' => date('U'),
            'last_ping_at' => date('U'),
        ];
    }

    /**
     * @throws \Exception
     */
    private function generateToken(): void
    {
        $this->token = $this->prefix.md5(random_bytes(64));
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }
}
