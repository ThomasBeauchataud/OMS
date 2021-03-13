<?php


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
     * @return bool
     */
    public function createFromWorkflowOrder(Order $order): bool
    {
        $output = false;
        $sender = $order->getSender();
        $pickers = $sender->getPickers();
        /** @var Stock[] $stocks */
        $stocks = $this->em->getRepository(Stock::class)->findBySenderEntityProduct($order);
        /** @var OrderRow $orderRow */
        foreach ($order->getOrderRows() as $orderRow) {
            $senderStock = array_key_exists($orderRow->getProduct(), $stocks) ? $stocks[$orderRow->getProduct()]->getQuantity() : 0;
            if ($senderStock < $orderRow->getQuantity()) {
                $remainder = $orderRow->getQuantity() - $senderStock;
                /** @var Picker $picker */
                foreach ($pickers as $picker) {
                    $pickerStock = $this->getSenderStock($picker->getPreparer(), $orderRow, $order);
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
                    $orderRow->setPreparation($preparation);
                    $this->em->persist($preparation);
                    $output = true;
                }
            }
        }
        if ($output) {
            $this->em->flush();
        }
        return $output;

    }

    /**
     * @param Sender $sender
     * @param OrderRow $orderRow
     * @param Order $order
     * @return bool
     */
    public function getSenderStock(Sender $sender, OrderRow $orderRow, Order $order): bool
    {
        /** @var Stock $stock */
        $stock = $this->em->getRepository(Stock::class)->findOneBy(array(
            'product' => $orderRow->getProduct(),
            'sender' => $sender,
            'entity' => $order->getTransmitter()->getEntity()
        ));
        return $stock === null ? 0 : $stock->getQuantity();
    }

}