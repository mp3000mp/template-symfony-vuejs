<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\TermsOfServiceSignature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TermsOfServiceSignature|null find($id, $lockMode = null, $lockVersion = null)
 * @method TermsOfServiceSignature|null findOneBy(array $criteria, array $orderBy = null)
 * @method TermsOfServiceSignature[]    findAll()
 * @method TermsOfServiceSignature[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TermsOfServiceSignatureRepository extends ServiceEntityRepository
{
    /**
     * UserRepository constructor.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TermsOfServiceSignature::class);
    }
}
