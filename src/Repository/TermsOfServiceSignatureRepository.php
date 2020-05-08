<?php

namespace App\Repository;

use App\Entity\TermsOfService;
use App\Entity\TermsOfServiceSignature;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
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
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TermsOfServiceSignature::class);
    }

}
