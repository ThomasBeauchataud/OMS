<?php


namespace App\Order\Service;


use App\Order\Entity\Order;

interface OrderDispatcher
{

    /**
     * @param Order $order
     */
    public function send(Order $order): void;

}