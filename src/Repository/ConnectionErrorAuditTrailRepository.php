<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\ConnectionErrorAuditTrail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ConnectionErrorAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConnectionErrorAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConnectionErrorAuditTrail[]    findAll()
 * @method ConnectionErrorAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConnectionErrorAuditTrailRepository extends ServiceEntityRepository
{
    /**
     * UserRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConnectionErrorAuditTrail::class);
    }
}
