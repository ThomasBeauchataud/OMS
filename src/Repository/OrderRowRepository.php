<?php

/**
 * Author Thomas Beauchataud
 * Since 14/03/2021
 */


namespace App\Repository;


use App\Entity\OrderRow;
use App\Entity\Preparation;
use App\Entity\Stock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrderRow|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderRow|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderRow[]    findAll()
 * @method OrderRow[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRowRepository extends ServiceEntityRepository
{

    /**
     * OrderRowRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderRow::class);
    }

}
