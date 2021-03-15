<?php

/**
 * Author Thomas Beauchataud
 * Since 14/03/2021
 */


namespace App\Messenger\Order;


use App\Entity\Order;

/**
 * An order importation message coming from a transporter
 */
class ImportOrderMessage
{

    /**
     * @var Order
     */
    protected Order $order;

    /**
     * OrderMessage constructor.
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }


    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

}