<?php

namespace App\Repository;

use App\Entity\Contribution;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Contribution|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contribution|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contribution[]    findAll()
 * @method Contribution[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContributionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contribution::class);
    }

    public function getContributionsByTrickId(int $trickId)
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.trick_id = :id')
            ->setParameter('id', $trickId);
        return $query->getQuery()->getResult();
    }

    static public function createNewTrickCriteria()
    {
        return Criteria::create()
            ->andWhere(Criteria::expr()->eq('trick', null));
    }
}
