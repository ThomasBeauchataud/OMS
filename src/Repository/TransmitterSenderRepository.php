<?php

/**
 * Author Thomas Beauchataud
 * Since 14/03/2021
 */


namespace App\Repository;


use App\Entity\TransmitterSender;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TransmitterSender|null find($id, $lockMode = null, $lockVersion = null)
 * @method TransmitterSender|null findOneBy(array $criteria, array $orderBy = null)
 * @method TransmitterSender[]    findAll()
 * @method TransmitterSender[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransmitterSenderRepository extends ServiceEntityRepository
{

    /**
     * TransmitterSenderRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TransmitterSender::class);
    }

}
