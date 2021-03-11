<?php


namespace App\Order\Service;


use App\Actor\Entity\Sender;
use App\Order\Entity\Order;
use App\Actor\Service\SenderRulerInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Catch the creation of a new order, assign his sender, if the order needs picking, pass the state to picking state
 * else to ready state
 *
 * Class OrderPersistListener
 * @package App\Order\Service
 */
class OrderPersistListener
{

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * @var SenderRulerInterface
     */
    protected SenderRulerInterface $senderRuler;

    /**
     * OrderPersistListener constructor.
     * @param EntityManagerInterface $em
     * @param SenderRulerInterface $senderRuler
     */
    public function __construct(EntityManagerInterface $em, SenderRulerInterface $senderRuler)
    {
        $this->em = $em;
        $this->senderRuler = $senderRuler;
    }


    /**
     * @param Order $order
     */
    public function postPersist(Order $order): void
    {
        if ($order->getState() !== Order::STATE_CREATED) {
            return;
        }
        if ($order->getSender() === null) {
            $sender = $this->senderRuler->chooseSender($order);
            $order->setSender($sender);
        }
        if ($this->senderHasEnoughStock($order->getSender(), $order->getOrderRows())) {
            $order->setState(Order::STATE_READY);
        } else {
            $order->setState(Order::STATE_GENERATE_PICKING);
        }
        $this->em->persist($order);
        $this->em->flush();
    }

    /**
     * @param Sender $sender
     * @param Collection $orderRows
     */
    private function senderHasEnoughStock(Sender $sender, Collection $orderRows): bool
    {
        return true; //TODO
    }

}