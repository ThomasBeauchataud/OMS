<?php


namespace App\Service;


use App\Entity\OrderRow;
use App\Entity\Sender;
use App\Entity\TransmitterSender;
use App\Entity\Order;
use App\Workflow\Order\SenderSelectorInterface;
use Doctrine\ORM\EntityManagerInterface;

class OrderService implements SenderSelectorInterface
{

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * OrderService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @inheritDoc
     */
    public function selectSenderForOrder(Order $order): Sender
    {
        $transmitterSenders = iterator_to_array($order->getTransmitter()->getTransmitterSenders());
        if ($this->orderContainsMedicine($order)) {
            $transmitterSenders = array_filter($transmitterSenders, function (TransmitterSender $transmitterSender) {
                return $transmitterSender->getSender()->isMedicineManager();
            });
        }
        $transmitterSenders = array_filter($transmitterSenders, function (TransmitterSender $transmitterSender) use ($order) {
            return $this->senderSupportsOrder($transmitterSender->getSender(), $order);
        });
        usort($transmitterSenders, function (TransmitterSender $ts1, TransmitterSender $ts2) {
            return $ts1->getPriority() <=> $ts2->getPriority();
        });
        return array_shift($transmitterSenders)->getSender();
    }

    /**
     * TODO
     * @param Order $order
     * @return bool
     */
    public function orderContainsMedicine(Order $order): bool
    {
        /** @var OrderRow $orderRow */
        foreach($order->getOrderRows() as $orderRow) {
            if ($orderRow->isMedicine()) {
                return true;
            }
        }
        return false;
    }

    /**
     * TODO
     * @param Sender $sender
     * @param Order $order
     * @return bool
     */
    public function senderSupportsOrder(Sender $sender, Order $order): bool
    {
        return true;
    }

}