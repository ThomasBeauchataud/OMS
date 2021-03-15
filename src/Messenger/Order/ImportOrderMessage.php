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
     * @var array
     */
    protected array $context;

    /**
     * OrderMessage constructor.
     * @param Order $order
     * @param array $context
     */
    public function __construct(Order $order, array $context = array())
    {
        $this->order = $order;
        $this->context = $context;
    }


    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

}