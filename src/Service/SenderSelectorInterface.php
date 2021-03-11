<?php


namespace App\Service;


use App\Entity\Sender;
use App\Entity\Order;

interface SenderSelectorInterface
{

    /**
     * @param Order $order
     * @return Sender
     */
    public function selectSender(Order $order): Sender;

}