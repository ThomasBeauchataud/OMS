<?php

/**
 * Author Thomas Beauchataud
 * Since 14/03/2021
 */


namespace App\Workflow\Order;


use App\Entity\Order;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\TransitionBlocker;

/**
 * Prevent an order the pass to the next workflow state
 *
 * Class OrderWorkflowGuardSubscriber
 * @package App\Workflow\Order
 */
class OrderWorkflowGuardSubscriber implements EventSubscriberInterface
{

    /**
     * @param GuardEvent $event
     */
    public function guardReady(GuardEvent $event): void
    {
        /** @var Order $order */
        $order = $event->getSubject();
        foreach($order->getOrderRows() as $orderRow) {
            $preparation = $orderRow->getPreparation();
            if ($preparation !== null && !$preparation->isClosed() && !$order->forceReadyState()) {
                $event->addTransitionBlocker(new TransitionBlocker('Order has preparation in progress', 0));
            }
        }
    }

    /**
     * @param GuardEvent $event
     */
    public function guardDelivered(GuardEvent $event): void
    {
        /** @var Order $order */
        $order = $event->getSubject();
        if (!$order->hasDeliveryNote()) {
            $event->addTransitionBlocker(new TransitionBlocker('Delivery note not received', 0));
        }
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return array(
            'workflow.order.guard.to_ready' => 'guardReady',
            'workflow.order.guard.to_delivered' => 'guardDelivered'
        );
    }

}