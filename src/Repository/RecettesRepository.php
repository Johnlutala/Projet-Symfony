<?php

namespace App\Repository;

use App\Entity\Recettes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recettes>
 */
class RecettesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recettes::class);
    }

    public function findPublicRecette(?int $nbRecette): array
    {
        
       $queryBuilder = $this->createQueryBuilder('r')
            ->where('r.IsPublic = 1')
            ->orderBy('r.creatDat', 'DESC');

            if($nbRecette !==0 || $nbRecette !== null)
            {
                $queryBuilder -> setMaxResults($nbRecette);
            }
            // ->setParameter('param', $param)
           
            return $queryBuilder->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Recettes[] Returns an array of Recettes objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Recettes
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
