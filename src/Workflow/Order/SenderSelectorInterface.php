<?php


namespace App\Workflow\Order;


use App\Entity\Sender;
use App\Entity\Order;

interface SenderSelectorInterface
{

    /**
     * Return the best sender for the order passed in parameter
     *
     * @param Order $order
     * @return Sender
     */
    public function selectSenderForOrder(Order $order): Sender;

}