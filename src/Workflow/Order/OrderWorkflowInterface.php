<?php

/**
 * Author Thomas Beauchataud
 * From 14/03/2021
 */


namespace App\Workflow\Order;


use App\Entity\Order;

interface OrderWorkflowInterface
{

    /**
     * @param Order $order
     */
    public function proceed(Order $order): void;

}