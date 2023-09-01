<?php

namespace App\Repository;

use App\Entity\GameBuilding;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameBuilding>
 *
 * @method GameBuilding|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameBuilding|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameBuilding[]    findAll()
 * @method GameBuilding[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameBuildingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameBuilding::class);
    }

//    /**
//     * @return GameBuilding[] Returns an array of GameBuilding objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?GameBuilding
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
