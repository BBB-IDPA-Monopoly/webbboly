<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\GameBuilding;
use App\Entity\Street;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameBuilding>
 *
 * @method GameBuilding|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameBuilding|null findOneBy(array $criteria, array|null $orderBy = null)
 * @method GameBuilding[]    findAll()
 * @method GameBuilding[]    findBy(array $criteria, array|null $orderBy = null, $limit = null, $offset = null)
 */
class GameBuildingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameBuilding::class);
    }

    public function save(GameBuilding $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(GameBuilding $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return GameBuilding[]
     */
    public function findByGameAndStreet(Game $game, Street $street): array
    {
        return $this->createQueryBuilder('gb')
            ->leftJoin('gb.building', 'b')
            ->andWhere('gb.game = :game')
            ->andWhere('b.street = :street')
            ->setParameter('game', $game)
            ->setParameter('street', $street)
            ->getQuery()
            ->getResult()
        ;
    }

    public function removeByGame(Game $game): void
    {
        $this->createQueryBuilder('gb')
            ->delete()
            ->andWhere('gb.game = :game')
            ->setParameter('game', $game)
            ->getQuery()
            ->execute()
        ;
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
