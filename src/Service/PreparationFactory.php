<?php

/**
 * Author Thomas Beauchataud
 * Since 14/03/2021
 */


namespace App\Service;


use App\Entity\Order;
use App\Entity\OrderRow;
use App\Entity\Picker;
use App\Entity\Preparation;
use App\Entity\Sender;
use App\Entity\Stock;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Create a preparation from an order and save it
 *
 * Class PreparationFactory
 * @package App\Service
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
     * @param Order $order
     * @return Preparation[]
     */
    public function create(Order $order): array
    {
        $sender = $order->getSender();
        $pickers = $sender->getPickers();
        $preparations = array();
        /** @var Stock[] $stocks */
        $stocks = $this->em->getRepository(Stock::class)->findBySenderEntityProducts($order);
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
                    $pickerStock = $this->getSenderStock($picker->getPreparer(), $orderRow);
                    if ($pickerStock === 0) {
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