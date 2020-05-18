<?php declare(strict_types=1);

namespace App\Service\DeviceSession;

use App\Entity\Application;
use App\Entity\ConnectionAuditTrail;
use App\Entity\User;
use App\Repository\ConnectionAuditTrailRepository;
use App\Service\Redis\RedisClient;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class DeviceSession
 *
 * @package App\Service\DeviceSession
 */
class DeviceSession
{
    private const prefix = 'DST_';

    /** @var EntityManagerInterface */
    private $em;
    /** @var RedisClient */
    private $redis;
    /** @var SessionInterface */
    private $session;
    /** @var Request  */
    private $request;
    /** @var string */
    private $deviceSessionToken;

    /**
     * DeviceSession constructor.
     *
     * @param EntityManagerInterface $em
     * @param RedisClient $redis
     * @param SessionInterface $session
     * @param RequestStack $request
     */
    public function __construct(EntityManagerInterface $em, RedisClient $redis, SessionInterface $session, RequestStack $request)
    {
        $this->em = $em;
        $this->redis = $redis;
        $this->request = $request->getCurrentRequest();
        $this->session = $session;
    }

    /**
     * @param string $deviceSessionToken
     *
     * @return bool
     */
    public function deviceSessionExists(string $deviceSessionToken): bool
    {
        return false !== $this->redis->get($deviceSessionToken);
    }

    /**
     * create device session when does'nt exist
     *
     * @param User $user
     *
     * @throws \Exception
     */
    public function createSession(User $user): void
    {
        // get portal
        /** @var Application $portal */
        $portal = $this->em->getRepository(Application::class)
            ->find(1)
        ;

        // generate device token
        $this->deviceSessionToken = $this->generateToken();

        // set session
        $this->session->set('device_session_token', $this->deviceSessionToken);
        $this->session->set('this_app', $portal->getId());

        // set session token in db
        $this->logConnection($user, $portal);

        // redis
        $this->initRedis($user, $portal);
    }

    /**
     * @param int $reason
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function destroy(string $deviceSessionToken, int $reason): void
    {
        // destroy redis entry
        $this->redis->delete($deviceSessionToken);

        // destroy in session
        $this->session->remove('device_session_token');

        // log logout
        /** @var ConnectionAuditTrailRepository $repConnAT */
        $repConnAT = $this->em->getRepository(ConnectionAuditTrail::class);
        $repConnAT->logoutDevice($deviceSessionToken, $reason);
    }

    /**
     * @param User $user
     * @param Application $portal
     *
     */
    private function initRedis(User $user, Application $portal): void
    {
        $this->redis->set($this->deviceSessionToken, [
            'startedAt' => date('U'),
            'deviceToken' => $this->deviceSessionToken,
            'portalId' => $portal->getId(),
            'userAgent' => $this->request->headers->get('user-agent'),
            'userId' => $user->getId(),
        ]);
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    private function generateToken(): string
    {
        return self::prefix.md5(random_bytes(64));
    }

    /**
     * @param User $user
     * @param Application $app
     */
    private function logConnection(User $user, Application $app): void
    {
        // log
        $log = new ConnectionAuditTrail();
        $log->setUser($user);
        $log->setStartedAt(new DateTime());
        $log->setApplication($app);
        $log->setIp($this->request->getClientIp());
        $log->setUseragent($this->request->headers->get('user-agent'));
        $log->setDeviceSessionToken($this->deviceSessionToken);

        // persist
        $this->em->persist($log);
        $this->em->flush();
    }
}
