<?php

namespace App\Repository;
use App\Entity\Bibliotheque;
use App\Entity\Jeu;
use App\Entity\Member;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Jeu>
 */
class JeuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Jeu::class);
    }


    public function findMemberJeux(Member $member): array
{
    return $this->createQueryBuilder('j')
        ->join('j.coffre', 'c')
        ->andWhere('c.membre = :member')
        ->setParameter('member', $member)
        ->getQuery()
        ->getResult();
}

public function remove(Jeu $entity, bool $flush = false): void
{
    $bibliothequeRepository = $this->getEntityManager()->getRepository(Bibliotheque::class);

    // Supprimer les associations ManyToMany avec les bibliothèques
    $bibliotheques = $bibliothequeRepository->findJeuBibliotheques($entity);   
    foreach ($bibliotheques as $bibliotheque) {
        $bibliotheque->removeJeux($entity);
        $this->getEntityManager()->persist($bibliotheque);
    }

    if ($flush) {
        $this->getEntityManager()->flush();
    }

    // Supprimer l'objet lui-même
    $this->getEntityManager()->remove($entity);

    if ($flush) {
        $this->getEntityManager()->flush();
    }
}


    //    /**
    //     * @return Jeu[] Returns an array of Jeu objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('j')
    //            ->andWhere('j.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('j.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Jeu
    //    {
    //        return $this->createQueryBuilder('j')
    //            ->andWhere('j.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
