<?php


namespace App\Workflow\Order;


use App\Entity\Order;

interface OrderValidatorInterface
{

    /**
     * Returns true if the sender has all the stock required to deliver the passed order
     *
     * @param Order $order
     * @return bool
     */
    public function hasSenderStockForOrder(Order $order): bool;

    /**
     * Returns true if the order passed in parameter has to be exported to the sender no matter the others conditions
     *
     * @param Order $order
     * @return bool
     */
    public function forceOrderExportation(Order $order): bool;

    /**
     * Returns true if the order passed in parameter has preparation not received by the sender
     *
     * @param Order $order
     * @return bool
     */
    public function hasOrderPreparationInProgress(Order $order): bool;

}