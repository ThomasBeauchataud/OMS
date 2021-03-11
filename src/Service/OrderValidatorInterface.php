<?php


namespace App\Service;


use App\Entity\Order;

interface OrderValidatorInterface
{

    /**
     * @param Order $order
     * @return bool
     */
    public function validateStock(Order $order): bool;

    /**
     * @param Order $order
     * @return bool
     */
    public function forceExportation(Order $order): bool;

}