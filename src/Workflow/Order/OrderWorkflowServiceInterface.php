<?php

/**
 * Author Thomas Beauchataud
 * From 14/03/2021
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

}