<?php

/**
 * Author Thomas Beauchataud
 * Since 14/03/2021
 */


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
     * Export the order to his transmitter when its delivered (when the order has a delivery note)
     *
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
     * Create needed preparation for an order
     *
     * @param Order $order
     */
    public function createNeededPreparation(Order $order): void;

    /**
     * Update available sender stock (realStock) after having associated the order to the sender
     *
     * @param Order $order
     */
    public function updateRealStock(Order $order): void;

}