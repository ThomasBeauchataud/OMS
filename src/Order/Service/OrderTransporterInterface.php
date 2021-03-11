<?php


namespace App\Order\Service;


use App\Order\Entity\Order;

interface OrderTransporterInterface
{

    /**
     * @param Order $order
     */
    public function transport(Order $order): void;

}