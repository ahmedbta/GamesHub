<?php

namespace App\Repository;

use App\Entity\Bibliotheque;
use App\Entity\Member;
use App\Entity\Jeu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Bibliotheque>
 */
class BibliothequeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bibliotheque::class);
    }

      /**
     * Retourne toutes les bibliothèques publiques.
     *
     * @return Bibliotheque[]
     */
    public function findPublic(): array
    {
        return $this->findBy(['publiee' => true]);
    }
    

    public function findAccessibleBibliotheques(Member $member): array
{
    return $this->createQueryBuilder('b')
        ->andWhere('b.publiee = :publiee OR b.createur = :createur')
        ->setParameter('publiee', true)
        ->setParameter('createur', $member)
        ->getQuery()
        ->getResult();
}

/**
 * @return Bibliotheque[] Returns an array of Bibliotheque objects
 */
public function findJeuBibliotheques(Jeu $jeu): array
{
    return $this->createQueryBuilder('b')
        ->leftJoin('b.jeux', 'j')
        ->andWhere('j = :jeu')
        ->setParameter('jeu', $jeu)
        ->getQuery()
        ->getResult();
}



    //    /**
    //     * @return Bibliotheque[] Returns an array of Bibliotheque objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Bibliotheque
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
