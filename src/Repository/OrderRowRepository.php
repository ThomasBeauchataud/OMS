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


    /**
     * @param Stock $stock
     * @return int|null
     * @throws Exception
     */
    public function findCountQuantityByEntitySenderProduct(Stock $stock): ?int
    {
        $query = 'SELECT sum(o0_.quantity) FROM order_row o0_ LEFT JOIN `order` o1_ ON o0_.order_id = o1_.id LEFT JOIN transmitter t2_ ON o1_.transmitter_id = t2_.id WHERE t2_.entity_id = ? AND o1_.sender_id = ? AND o0_.product = ? AND o0_.preparation_id IS NULL';
        return $this->_em->getConnection()->fetchOne($query, array($stock->getEntity()->getId(), $stock->getSender()->getId(), $stock->getProduct()));
    }

}
