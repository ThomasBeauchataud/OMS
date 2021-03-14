<?php


namespace App\Messenger;


use App\Entity\Order;

/**
 * Class OrderMessage
 * @package App\Messenger
 */
class OrderMessage
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