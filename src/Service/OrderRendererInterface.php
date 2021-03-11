<?php


namespace App\Service;


use App\Entity\Order;

interface OrderRendererInterface
{

    /**
     * @param Order $order
     */
    public function render(Order $order): void;

}