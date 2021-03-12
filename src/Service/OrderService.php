<?php


namespace App\Service;


use App\Entity\Sender;
use App\Entity\TransmitterSender;
use App\Entity\WorkflowOrder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class OrderService implements OrderRendererInterface, OrderValidatorInterface, SenderSelectorInterface
{

    /**
     * @var SerializerInterface
     */
    protected SerializerInterface $serializer;

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * OrderService constructor.
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     */
    public function __construct(SerializerInterface $serializer, EntityManagerInterface $em)
    {
        $this->serializer = $serializer;
        $this->em = $em;
    }


    /**
     * TODO
     * @inheritDoc
     */
    public function render(WorkflowOrder $workflowOrder): void
    {

    }

    /**
     * TODO
     * @inheritDoc
     */
    public function validateStock(WorkflowOrder $workflowOrder): bool
    {
        return false;
    }

    /**
     * TODO
     * @inheritDoc
     */
    public function forceExportation(WorkflowOrder $workflowOrder): bool
    {
        return false;
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

}