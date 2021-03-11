<?php


namespace App\Service;


use App\Entity\Order;

interface OrderExporterInterface
{

    /**
     * @param Order $order
     */
    public function exportOrder(Order $order): void;

}