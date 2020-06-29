<?php

namespace App\Repository;

use App\Entity\Annonces;
use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use League\Uri\Exception;

/**
 * @method Annonces|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annonces|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annonces[]    findAll()
 * @method Annonces[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnoncesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonces::class);
    }

    // /**
    //  * @return Annonces[] Returns an array of Annonces objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Annonces
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findByUser(Users $user)
    {
        $query = $this->createQueryBuilder('a')
            ->where(':user MEMBER OF a.User')
            ->setParameter('user', $user)
            ->getQuery();

        $result = $query->getResult();

        return $result;
    }

    public function getPrixMin($NombrePieces, $SurfaceHabitable, $Secteur)
    {
        $query = $this->createQueryBuilder('pmin');
        $query->select('MIN(pmin.Prix) AS prix_min');
        if ($NombrePieces != null)
            $query->andWhere('pmin.NombrePieces = :nbPieces')->setParameter('nbPieces', $NombrePieces);
        if ($SurfaceHabitable != null)
            $query->andWhere('pmin.SurfaceHabitable = :surfaceHabitable')->setParameter('surfaceHabitable', $SurfaceHabitable);
        if ($Secteur != null)
            $query->andWhere('pmin.Secteur = :secteur')->setParameter('secteur', $Secteur);
        $query->orderBy('prix_min', 'DESC');


        return $query->getQuery()->getSingleResult();

    }

    public function getPrixMax($NombrePieces, $SurfaceHabitable, $Secteur)
    {
        $query = $this->createQueryBuilder('pmax');
        $query->select('MAX(pmax.Prix) AS prix_max');
        if ($NombrePieces != null)
            $query->andWhere('pmax.NombrePieces = :nbPieces')->setParameter('nbPieces', $NombrePieces);
        if ($SurfaceHabitable != null)
            $query->andWhere('pmax.SurfaceHabitable = :surfaceHabitable')->setParameter('surfaceHabitable', $SurfaceHabitable);
        if ($Secteur != null)
            $query->andWhere('pmax.Secteur = :secteur')->setParameter('secteur', $Secteur);
        $query->orderBy('prix_max', 'DESC');

        return $query->getQuery()->getSingleResult();
    }

    public function getPrixMoy($NombrePieces, $SurfaceHabitable, $Secteur)
    {
        $query = $this->createQueryBuilder('pmoy');
        $query->select('AVG(pmoy.Prix) AS prix_moy');
        if ($NombrePieces != null)
            $query->andWhere('pmoy.NombrePieces = :nbPieces')->setParameter('nbPieces', $NombrePieces);
        if ($SurfaceHabitable != null)
            $query->andWhere('pmoy.SurfaceHabitable = :surfaceHabitable')->setParameter('surfaceHabitable', $SurfaceHabitable);
        if ($Secteur != null)
            $query->andWhere('pmoy.Secteur = :secteur')->setParameter('secteur', $Secteur);
        $query->orderBy('prix_moy', 'DESC');

        try {
            return $query->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
        } catch (NonUniqueResultException $e) {
        }
        return null;
    }

    public function existeDeja(Annonces $annonce)
    {
        $res = $this->findOneBy(['Reference' => $annonce->getReference()]);
        try {
            if ($res != null) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            throw new Exception("Erreur ExisteDeja : " . $e->getMessage());

        }
    }

    public function prixParJour()
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT MIN(a.prix) as \'prixMin\',MAX(a.prix) as \'prixMax\',AVG(a.prix) as \'prixMoy\',DATE_FORMAT(a.dateSuivi,"%Y-%m-%d") as \'date\' from annonce_prix_suivi a group by DATE_FORMAT(a.dateSuivi,"%Y-%m-%d")';
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();

    }
}
