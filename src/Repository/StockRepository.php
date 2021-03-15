<?php

/**
 * Author Thomas Beauchataud
 * Since 14/03/2021
 */


namespace App\Repository;


use App\Entity\Entity;
use App\Entity\Order;
use App\Entity\OrderRow;
use App\Entity\Sender;
use App\Entity\Stock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Stock|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stock|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stock[]    findAll()
 * @method Stock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockRepository extends ServiceEntityRepository
{

    /**
     * StockRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stock::class);
    }

    /**
     * @param Sender $sender
     * @param Entity $entity
     */
    public function removeFromSenderEntity(Sender $sender, Entity $entity): void
    {
        $this->createQueryBuilder('s')
            ->delete()
            ->where('s.entity = :entity')
            ->andWhere('s.sender = :sender')
            ->setParameter('sender', $sender)
            ->setParameter('entity', $entity)
            ->getQuery()
            ->execute();
    }

    /**
     * @param Order $order
     * @return Stock[]
     */
    public function findBySenderEntityProducts(Order $order): array
    {
        $products = array_map(function (OrderRow $orderRow) {
            return $orderRow->getProduct();
        }, iterator_to_array($order->getOrderRows()));
        $responses = $this->createQueryBuilder('st')
            ->where('st.entity = :entity')
            ->andWhere('st.sender = :sender')
            ->andWhere('st.product IN (:products)')
            ->setParameter('sender', $order->getSender())
            ->setParameter('entity', $order->getTransmitter()->getEntity())
            ->setParameter('products', $products)
            ->getQuery()
            ->getResult();
        $output = array();
        foreach ($responses as $response) {
            $output[$response->getProduct()] = $response;
        }
        return $output;
    }

    /**
     * @param OrderRow $orderRow
     * @param Sender|null $sender
     * @return Stock|null
     * @throws NonUniqueResultException
     */
    public function findBySenderEntityProduct(OrderRow $orderRow, Sender $sender = null): ?Stock
    {
        return $this->createQueryBuilder('st')
            ->where('st.entity = :entity')
            ->andWhere('st.sender = :sender')
            ->andWhere('st.product = :product')
            ->setParameter('sender', $sender === null ? $orderRow->getOrder()->getSender() : $sender)
            ->setParameter('entity', $orderRow->getOrder()->getTransmitter()->getEntity())
            ->setParameter('product', $orderRow->getProduct())
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function updateRealStock(): void
    {
        $this->_em->getConnection()->prepare('CALL update_real_stock ()')->execute();
    }

    /**
     * @param Entity $entity
     * @param Sender $sender
     * @param array $products
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function updateRealStockProduct(Entity $entity, Sender $sender, array $products): void
    {
        $query = 'CALL update_real_stock_product (?, ?, ?)';
        $this->_em->getConnection()->prepare($query)->execute([$entity->getId(), $sender->getId(), implode(',', $products)]);
    }

}
