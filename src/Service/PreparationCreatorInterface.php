<?php


namespace App\Service;


use App\Entity\Order;

interface PreparationCreatorInterface
{

    /**
     * @param Order $order
     */
    public function createPreparations(Order $order): void;

}