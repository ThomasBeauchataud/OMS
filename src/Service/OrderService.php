<?php


namespace App\Service;


use App\Entity\OrderRow;
use App\Entity\Sender;
use App\Entity\Stock;
use App\Entity\TransmitterSender;
use App\Entity\WorkflowOrder;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class OrderService implements OrderValidatorInterface, SenderSelectorInterface
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
    public function validateStock(WorkflowOrder $workflowOrder): bool
    {
        foreach($workflowOrder->getOrderRows() as $orderRow) {
            $sender = $workflowOrder->getSender();
            $senderStock = $this->getSenderStock($sender, $orderRow);
            if ($senderStock < $orderRow->getQuantity()) {
                return false;
            }
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function forceExportation(WorkflowOrder $workflowOrder): bool
    {
        $now = new DateTime();
        return $now->diff($workflowOrder->getLastUpdate())->h > 36;
    }

    /**
     * @inheritDoc
     */
    public function selectSender(WorkflowOrder $workflowOrder): Sender
    {
        $order = $workflowOrder;
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
            return $ts1->getPriority() < $ts2->getPriority();
        });
        return array_shift($transmitterSenders)->getSender();
    }

    /**
     * TODO
     * @param WorkflowOrder $workflowOrder
     * @return bool
     */
    public function orderContainsMedicine(WorkflowOrder $workflowOrder): bool
    {
        /** @var OrderRow $orderRow */
        foreach($workflowOrder->getOrderRows() as $orderRow) {
            if ($orderRow->isMedicine()) {
                return true;
            }
        }
        return false;
    }

    /**
     * TODO
     * @param Sender $sender
     * @param WorkflowOrder $workflowOrder
     * @return bool
     */
    public function senderSupportsOrder(Sender $sender, WorkflowOrder $workflowOrder): bool
    {
        return true;
    }

    /**
     * @param Sender $sender
     * @param OrderRow $orderRow
     * @return bool
     */
    public function getSenderStock(Sender $sender, OrderRow $orderRow): bool
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