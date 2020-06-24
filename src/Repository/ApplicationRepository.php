<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Application;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Application|null find($id, $lockMode = null, $lockVersion = null)
 * @method Application|null findOneBy(array $criteria, array $orderBy = null)
 * @method Application[]    findAll()
 * @method Application[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicationRepository extends ServiceEntityRepository
{
    /**
     * UserRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Application::class);
    }

    /**
     * @param User $user
     * @param Application $application
     *
     * @return bool
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function hasUserAccess(User $user, Application $application)
    {
        $q = $this->createQueryBuilder('a')
            ->innerJoin('a.users', 'u')
            ->select('COUNT(1)')
            ->where('a = :application')
            ->andWhere('u = :user')
            ->setParameter('application', $application)
            ->setParameter('user', $user)
            ->getQuery();
        return $q->getSingleScalarResult() > 0;
    }

}
