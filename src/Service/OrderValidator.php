<?php


namespace App\Service;


use App\Entity\OrderRow;
use App\Entity\Sender;
use App\Entity\Stock;
use App\Entity\Order;
use App\Workflow\Order\OrderValidatorInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class OrderValidator implements OrderValidatorInterface
{

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * OrderValidator constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    /**
     * @inheritdoc
     */
    public function hasSenderStockForOrder(Order $order): bool
    {
        $stocks = $this->em->getRepository(Stock::class)->findBySenderEntityProduct($order);
        foreach($order->getOrderRows() as $orderRow) {

            $senderStock = array_key_exists($orderRow->getProduct(), $stocks) ? $stocks[$orderRow->getProduct()]->getQuantity() : 0;
            if ($senderStock < $orderRow->getQuantity()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Force the exportation of the order when its waiting for more than 36 hours
     *
     * @inheritdoc
     */
    public function forceOrderExportation(Order $order): bool
    {
        $now = new DateTime();
        return $now->diff($order->getLastUpdate())->h > 36;
    }

    /**
     * @inheritdoc
     */
    public function hasOrderPreparationInProgress(Order $order): bool
    {
        /** @var OrderRow $orderRow */
        foreach($order->getOrderRows() as $orderRow) {
            $preparation = $orderRow->getPreparation();
            if ($preparation !== null && !$preparation->isClosed()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Sender $sender
     * @param OrderRow $orderRow
     * @return bool
     */
    protected function getSenderStock(Sender $sender, OrderRow $orderRow): bool
    {
        /** @var Stock $stock */
        $stock = $this->em->getRepository(Stock::class)->findOneBy(array(
            'product' => $orderRow->getProduct(),
            'sender' => $sender,
            'entity' => $orderRow->getOrder()->getTransmitter()->getEntity()
        ));
        return $stock === null ? 0 : $stock->getQuantity();
    }

}