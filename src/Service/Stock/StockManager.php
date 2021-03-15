<?php

/**
 * Author Thomas Beauchataud
 * Since 14/03/2021
 */


namespace App\Service\Stock;


use App\Entity\Entity;
use App\Entity\OrderRow;
use App\Entity\Preparation;
use App\Entity\Sender;
use App\Entity\Stock;
use Doctrine\ORM\EntityManagerInterface;

class StockManager implements StockManagerInterface
{

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * StockManager constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    /**
     * @inheritdoc
     */
    public function importStocks(array $stocks, Sender $sender, Entity $entity): void
    {
        $this->em->getRepository(Stock::class)->removeFromSenderEntity($sender, $entity);
        foreach ($stocks as $stock) {
            if (($quantity = $stock->getQuantity()) > 0) {
                $this->em->persist($stock);
            }
        }
        $this->em->flush();
    }

    /**
     * Call a stored procedure
     *
     * @inheritdoc
     */
    public function updateRealStocks(Entity $entity, Sender $sender, array $products)
    {
        $this->em->getRepository(Stock::class)->updateRealStockProduct($entity, $sender, $products);
    }
}