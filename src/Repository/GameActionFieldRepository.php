<?php

namespace App\Repository;

use App\Entity\GameActionField;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameActionField>
 *
 * @method GameActionField|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameActionField|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameActionField[]    findAll()
 * @method GameActionField[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameActionFieldRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameActionField::class);
    }

    public function save(GameActionField $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(GameActionField $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param int $gameId
     * @param string $function
     * @return GameActionField[]
     */
    public function findByGameAndFunction(int $gameId, string $function): array
    {
        return $this->createQueryBuilder('gameActionField')
            ->leftJoin('gameActionField.actionField', 'actionField')
            ->andWhere('gameActionField.game = :gameId')
            ->andWhere('actionField.function = :function')
            ->setParameter('gameId', $gameId)
            ->setParameter('function', $function)
            ->getQuery()
            ->getResult()
        ;
    }

    //find by game and position

    /**
     * @param int $gameId
     * @param int $position
     * @return GameActionField|null
     * @throws NonUniqueResultException
     */
    public function findByGameAndPosition(int $gameId, int $position): GameActionField|null
    {
        return $this->createQueryBuilder('gameActionField')
            ->leftJoin('gameActionField.actionField', 'actionField')
            ->andWhere('gameActionField.game = :gameId')
            ->andWhere('actionField.position = :position')
            ->setParameter('gameId', $gameId)
            ->setParameter('position', $position)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }


//    /**
//     * @return GameActionField[] Returns an array of GameActionField objects
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

//    public function findOneBySomeField($value): ?GameActionField
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
