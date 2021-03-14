<?php


namespace App\Workflow\Order;


use App\Entity\Order;
use App\Entity\Sender;

interface OrderWorkflowServiceInterface
{

    /**
     * @param Order $order
     */
    public function exportToSender(Order $order): void;

    /**
     * @param Order $order
     */
    public function exportToTransmitter(Order $order): void;

    /**
     * Return the best sender for the order passed in parameter
     *
     * @param Order $order
     * @return Sender
     */
    public function selectSenderForOrder(Order $order): Sender;

    /**
     * Returns true if the sender has all the stock required to deliver the passed order
     *
     * @param Order $order
     * @return bool
     */
    public function hasSenderStockForOrder(Order $order): bool;

}