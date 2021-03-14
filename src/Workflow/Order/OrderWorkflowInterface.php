<?php


namespace App\Workflow\Order;


use App\Entity\Order;

interface OrderWorkflowInterface
{

    /**
     * @param Order $order
     */
    public function proceed(Order $order): void;

}