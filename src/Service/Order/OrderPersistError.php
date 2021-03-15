<?php

/**
 * Author Thomas Beauchataud
 * From 15/03/2021
 */


namespace App\Service\Order;


class OrderPersistError
{

    /**
     * @var 
     */
    protected $passedOrder;

    /**
     * @var string
     */
    protected string $reason;

    /**
     * OrderPersistError constructor.
     * @param $passedOrder
     * @param string $reason
     */
    public function __construct($passedOrder, string $reason)
    {
        $this->passedOrder = $passedOrder;
        $this->reason = $reason;
    }


    /**
     * @return mixed
     */
    public function getPassedOrder()
    {
        return $this->passedOrder;
    }

    /**
     * @return string
     */
    public function getReason(): string
    {
        return $this->reason;
    }

}