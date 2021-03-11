<?php


namespace App\Actor\Service;


use App\Actor\Entity\Sender;
use App\Order\Entity\Order;

interface SenderRulerInterface
{

    /**
     * @param Order $order
     * @return Sender
     */
    public function chooseSender(Order $order): Sender;

}