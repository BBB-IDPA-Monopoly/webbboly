<?php

namespace App\Repository;

use App\Entity\ActionField;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ActionField>
 *
 * @method ActionField|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActionField|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActionField[]    findAll()
 * @method ActionField[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActionFieldRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActionField::class);
    }

//    /**
//     * @return ActionField[] Returns an array of ActionField objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ActionField
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
