<?php

/**
 * Author Thomas Beauchataud
 * Since 14/03/2021
 */


namespace App\Service\Stock;


use App\Entity\Entity;
use App\Entity\Sender;
use App\Entity\Stock;

interface StockManagerInterface
{

    /**
     * Import stock is the application
     *
     * @param Stock[] $stocks The original stocks received from the sender without any quantity operation
     * @param Sender $sender
     * @param Entity $entity
     */
    public function importStocks(array $stocks, Sender $sender, Entity  $entity): void;

    /**
     * @param Entity $entity
     * @param Sender $sender
     * @param array $products
     * @return mixed
     */
    public function updateRealStocks(Entity $entity, Sender $sender, array $products);

}