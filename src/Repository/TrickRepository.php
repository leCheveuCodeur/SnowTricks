<?php

namespace App\Repository;

use App\Entity\Trick;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Trick|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trick|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trick[]    findAll()
 * @method Trick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trick::class);
    }


    static public function createImgInFrontCriteria()
    {
        return Criteria::create()
            ->andWhere(Criteria::expr()->eq('in_front', 1));
    }

    public function getLoadMoreTricks(string $page, int $limit)
    {
        $query = $this->createQueryBuilder('t')
            ->setMaxResults($limit * $page);
        return $query->getQuery()->getResult();
    }
}
