<?php


namespace App\Order\Service;


use App\Order\Entity\Order;
use App\Order\Messenger\DispatchOrderMessage;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Catch the state transition of an order when it is ready to be send and dispatch the preparation order
 *
 * Class OrderListener
 * @package App\Order\Service
 */
class OrderUpdateListener
{

    /**
     * @var MessageBusInterface
     */
    protected MessageBusInterface $bus;

    /**
     * OrderUpdateListener constructor.
     * @param MessageBusInterface $bus
     */
    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }


    /**
     * @param Order $order
     */
    public function postUpdate(Order $order): void
    {
        if ($order->getState() === Order::STATE_READY) {
            $this->bus->dispatch(new DispatchOrderMessage($order));
        }
        if ($order->getState() === Order::STATE_GENERATE_PICKING) {
            $this->bus->dispatch(new DispatchOrderMessage($order)); //TODO
        }
    }

}