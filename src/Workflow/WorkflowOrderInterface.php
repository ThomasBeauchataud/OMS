<?php


namespace App\Workflow;


use App\Entity\Order;

interface WorkflowOrderInterface
{

    /**
     * @param Order $order
     */
    public function proceed(Order $order): void;

}