<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\GameCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameCard>
 *
 * @method GameCard|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameCard|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameCard[]    findAll()
 * @method GameCard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameCardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameCard::class);
    }

    public function save(GameCard $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(GameCard $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function removeByGame(Game $game): void
    {
        $this->createQueryBuilder('gc')
            ->delete()
            ->andWhere('gc.game = :game')
            ->setParameter('game', $game)
            ->getQuery()
            ->execute()
        ;
    }

//    /**
//     * @return GameCard[] Returns an array of GameCard objects
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

//    public function findOneBySomeField($value): ?GameCard
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
