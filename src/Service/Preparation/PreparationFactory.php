<?php

/**
 * Author Thomas Beauchataud
 * Since 14/03/2021
 */


namespace App\Service\Preparation;


use App\Entity\Order;
use App\Entity\OrderRow;
use App\Entity\Picker;
use App\Entity\Preparation;
use App\Entity\Sender;
use App\Entity\Stock;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Create needed preparation fo an order
 */
class PreparationFactory
{

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * PreparationCreator constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    /**
     * Create needed preparation fo an order
     * Scan all rows of the order
     * If the sender doesn't have enough stock for a row order
     * Foreach picker order in priority order
     * If the picker can help with stock
     * Create a preparation
     *
     * @param Order $order
     * @return Preparation[]
     */
    public function create(Order $order): array
    {
        $sender = $order->getSender();
        $client = $order->getTransmitter()->getEntity();
        $pickers = $this->em->getRepository(Picker::class)->findBy(['clientEntity' => $client, 'client' => $sender]);
        usort($pickers, function (Picker $pk1, Picker $pk2) {
            return $pk1->getPriority() <=> $pk2->getPriority();
        });
        $preparations = array();
        /** @var Stock[] $stocks */
        $stocks = $this->em->getRepository(Stock::class)->findBySenderEntityProducts($order, $sender, $client);
        /** @var OrderRow $orderRow */
        foreach ($order->getOrderRows() as $orderRow) {
            $senderStock = array_key_exists($orderRow->getProduct(), $stocks) ? $stocks[$orderRow->getProduct()]->getRealQuantity() : 0;
            if ($senderStock < $orderRow->getQuantity()) {
                $remainder = $orderRow->getQuantity() - $senderStock;
                if ($remainder === 0) {
                    continue;
                }
                /** @var Picker $picker */
                foreach ($pickers as $picker) {
                    /** @var ?Stock $pickerStock */
                    $pickerStock = $this->em->getRepository(Stock::class)
                        ->findBySenderEntityProduct($orderRow, $picker->getPreparer(), $picker->getPreparerEntity());
                    $pickerStock = $pickerStock === null ? 0 : $pickerStock->getRealQuantity(); //TODO GET AVAILABLE PICKING STOCK DEFINED BY THE ENTITY AND NOT THE REAL STOCK
                    if ($pickerStock <= 0) {
                        continue;
                    }
                    if ($pickerStock > $remainder) {
                        $preparationQuantity = $remainder;
                    } else {
                        $remainder -= $pickerStock;
                        $preparationQuantity = $pickerStock;
                    }
                    $preparation = new Preparation();
                    $preparation->setPicker($picker);
                    $preparation->setProduct($orderRow->getProduct());
                    $preparation->setQuantity($preparationQuantity);
                    $preparation->setOrderRow($orderRow);
                    $preparations[] = $preparation;
                }
            }
        }
        return $preparations;

    }

    /**
     * @param Sender $sender
     * @param OrderRow $orderRow
     * @return int
     */
    private function getSenderStock(Sender $sender, OrderRow $orderRow): int
    {
        /** @var Stock $stock */
        $stock = $this->em->getRepository(Stock::class)->findBySenderEntityProduct($orderRow, $sender);
        return $stock === null ? 0 : $stock->getRealQuantity();
    }

}