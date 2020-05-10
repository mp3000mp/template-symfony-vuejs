<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\TermsOfService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TermsOfService|null find($id, $lockMode = null, $lockVersion = null)
 * @method TermsOfService|null findOneBy(array $criteria, array $orderBy = null)
 * @method TermsOfService[]    findAll()
 * @method TermsOfService[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TermsOfServiceRepository extends ServiceEntityRepository
{
    /**
     * UserRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TermsOfService::class);
    }

    /**
     * @return TermsOfService|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findLast(): ?TermsOfService
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.published_at', 'DESC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
