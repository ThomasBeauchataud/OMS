<?php


namespace App\Order\Messenger;


use App\Order\Entity\Order;

class DispatchOrderMessage
{

    /**
     * @var Order
     */
    protected Order $order;

    /**
     * PreparationOrder constructor.
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