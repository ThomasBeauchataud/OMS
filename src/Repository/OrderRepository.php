<?php

/**
 * Author Thomas Beauchataud
 * Since 14/03/2021
 */


namespace App\Repository;


use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{

    /**
     * OrderRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }


    /**
     * @param Order $order
     */
    public function updateState(Order $order): void
    {
        $this->createQueryBuilder("o")
            ->update()
            ->set('o.state', ':state')
            ->set('o.lastUpdate', ':update')
            ->where('o.id = :id')
            ->setParameter('state', $order->getState())
            ->setParameter('update', $order->getLastUpdate()->format('Y-m-d h:i:s'))
            ->setParameter('id', $order->getId())
            ->getQuery()
            ->execute();
    }

    /**
     * @param Order $order
     */
    public function updateClosed(Order $order): void
    {
        $this->createQueryBuilder("o")
            ->update()
            ->set('o.closed', ':closed')
            ->where('o.id = :id')
            ->setParameter('closed', $order->isClosed())
            ->setParameter('id', $order->getId())
            ->getQuery()
            ->execute();
    }

    /**
     * @param Order $order
     */
    public function updateStateAndIncomplete(Order $order): void
    {
        $this->createQueryBuilder("o")
            ->update()
            ->set('o.state', ':state')
            ->set('o.lastUpdate', ':update')
            ->set('o.forcedIncomplete', ':incomplete')
            ->where('o.id = :id')
            ->setParameter('state', $order->getState())
            ->setParameter('update', $order->getLastUpdate()->format('Y-m-d h:i:s'))
            ->setParameter('incomplete', $order->forceReadyState())
            ->setParameter('id', $order->getId())
            ->getQuery()
            ->execute();
    }

    /**
     * @param Order $order
     */
    public function updateSender(Order $order): void
    {
        $this->createQueryBuilder("o")
            ->update()
            ->set('o.sender', ':sender')
            ->where('o.id = :id')
            ->setParameter('sender', $order->getSender())
            ->setParameter('id', $order->getId())
            ->getQuery()
            ->execute();
    }
    
}
