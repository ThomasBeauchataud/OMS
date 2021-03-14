<?php

/**
 * Author Thomas Beauchataud
 * From 14/03/2021
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
                $realQuantity = $quantity;
                //$preparationQuantity = $this->em->getRepository(Preparation::class)->findCountQuantityByEntitySenderProduct($stock) ?? 0;
                //$orderQuantity = $this->em->getRepository(OrderRow::class)->findCountQuantityByEntitySenderProduct($stock) ?? 0;
                //$realQuantity -= $preparationQuantity;
                //$realQuantity -= $orderQuantity;
                if ($realQuantity > 0) {
                    $stock->setRealQuantity($realQuantity);
                    $this->em->persist($stock);
                }
            }
        }
        $this->em->flush();
    }

}