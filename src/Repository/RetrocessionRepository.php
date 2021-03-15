<?php


namespace App\Repository;


use App\Entity\Retrocession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Retrocession|null find($id, $lockMode = null, $lockVersion = null)
 * @method Retrocession|null findOneBy(array $criteria, array $orderBy = null)
 * @method Retrocession[]    findAll()
 * @method Retrocession[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RetrocessionRepository extends ServiceEntityRepository
{

    /**
     * RetrocessionRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Retrocession::class);
    }

}
