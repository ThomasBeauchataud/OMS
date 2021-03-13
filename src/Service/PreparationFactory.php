<?php


namespace App\Service;


use App\Entity\OrderRow;
use App\Entity\Preparation;
use App\Entity\Sender;
use App\Entity\Stock;
use App\Entity\WorkflowOrder;
use Doctrine\ORM\EntityManagerInterface;

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
     * @param WorkflowOrder $workflowOrder
     */
    public function createFromWorkflowOrder(WorkflowOrder $workflowOrder): void
    {
        foreach($workflowOrder->getOrderRows() as $orderRow) {
            $sender = $workflowOrder->getSender();
            $senderStock = $this->getSenderStock($sender, $orderRow);
            if ($senderStock < $orderRow->getQuantity()) {
                $remainder = $orderRow->getQuantity() - $senderStock;
                /** @var Sender $picker */
                foreach($sender->getPickers() as $picker) {
                    $pickerStock = $this->getSenderStock($picker, $orderRow);
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
                    $this->em->persist($preparation);
                }
            }
        }
        $this->em->flush();
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