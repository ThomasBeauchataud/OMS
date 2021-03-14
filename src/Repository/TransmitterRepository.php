<?php

/**
 * Author Thomas Beauchataud
 * From 14/03/2021
 */


namespace App\Repository;


use App\Entity\Transmitter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Transmitter|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transmitter|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transmitter[]    findAll()
 * @method Transmitter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransmitterRepository extends ServiceEntityRepository
{

    /**
     * TransmitterRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transmitter::class);
    }

}
