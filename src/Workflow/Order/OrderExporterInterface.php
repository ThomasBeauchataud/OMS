<?php


namespace App\Workflow\Order;


use App\Entity\Order;

interface OrderExporterInterface
{

    /**
     * @param Order $order
     */
    public function exportToSender(Order $order): void;

    /**
     * @param Order $order
     */
    public function exportToTransmitter(Order $order): void;

}