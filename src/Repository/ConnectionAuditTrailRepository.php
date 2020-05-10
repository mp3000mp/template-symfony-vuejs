<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\ConnectionAuditTrail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ConnectionAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConnectionAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConnectionAuditTrail[]    findAll()
 * @method ConnectionAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConnectionAuditTrailRepository extends ServiceEntityRepository
{
    /**
     * UserRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConnectionAuditTrail::class);
    }
}
