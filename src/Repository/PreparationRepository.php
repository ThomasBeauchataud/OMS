<?php

/**
 * Author Thomas Beauchataud
 * From 14/03/2021
 */


namespace App\Repository;


use App\Entity\Preparation;
use App\Entity\Stock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Preparation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Preparation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Preparation[]    findAll()
 * @method Preparation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PreparationRepository extends ServiceEntityRepository
{

    /**
     * PreparationRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Preparation::class);
    }


    /**
     * @param Stock $stock
     * @return int|null
     */
    public function findCountQuantityByEntitySenderProduct(Stock $stock): ?int
    {
        return $this->createQueryBuilder('p')
            ->select('sum(p.quantity)')
            ->leftJoin('p.orderRow', 'odr')
            ->leftJoin('odr.order', 'o')
            ->leftJoin('o.transmitter', 't')
            ->where('t.entity = :entity')
            ->andWhere('o.sender = :sender')
            ->andWhere('odr.product = :product')
            ->setParameter('entity', $stock->getEntity())
            ->setParameter('sender', $stock->getSender())
            ->setParameter('product', $stock->getProduct())
            ->getQuery()
            ->getFirstResult();
    }

    /**
     * @param Preparation $preparation
     */
    public function updateState(Preparation $preparation): void
    {
        $this->createQueryBuilder("p")
            ->update()
            ->set('p.state', ':state')
            ->set('p.lastUpdate', ':update')
            ->where('p.id = :id')
            ->setParameter('state', json_encode($preparation->getState()))
            ->setParameter('update', $preparation->getLastUpdate()->format('Y-m-d h:i:s'))
            ->setParameter('id', $preparation->getId())
            ->getQuery()
            ->execute();
    }

    /**
     * @param Preparation $preparation
     */
    public function updateClosed(Preparation $preparation): void
    {
        $this->createQueryBuilder("p")
            ->update()
            ->set('p.closed', ':closed')
            ->where('p.id = :id')
            ->setParameter('closed', $preparation->isClosed())
            ->setParameter('id', $preparation->getId())
            ->getQuery()
            ->execute();
    }

    /**
     * @param Preparation[] $preparations
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function insertMultiple(array $preparations): void
    {
        $query = "INSERT INTO preparation (picker_id, product, quantity, state, last_update, closed) VALUES ";
        foreach ($preparations as $preparation) {
            $values = "(" . $preparation->getPicker()->getId() . ","
                . "'" . $preparation->getProduct() . "',"
                . $preparation->getQuantity() . ","
                . "'" . json_encode($preparation->getState()) . "',"
                . "'" . $preparation->getLastUpdate()->format('Y-m-d h:i:s') . "',"
                . ($preparation->isClosed() ? 1 : 0) . "),";
            $query .= $values;
        }
        $query = substr($query, 0, -1);
        $this->getEntityManager()->getConnection()->prepare($query)->execute();
    }

}
