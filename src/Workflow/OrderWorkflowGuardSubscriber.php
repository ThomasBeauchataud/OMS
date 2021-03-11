<?php


namespace App\Workflow;


use App\Entity\Order;
use App\Service\OrderValidatorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\TransitionBlocker;

class OrderWorkflowGuardSubscriber implements EventSubscriberInterface
{

    /**
     * @var OrderValidatorInterface
     */
    protected OrderValidatorInterface $orderValidator;

    /**
     * OrderWorkflowGuardSubscriber constructor.
     * @param OrderValidatorInterface $orderValidator
     */
    public function __construct(OrderValidatorInterface $orderValidator)
    {
        $this->orderValidator = $orderValidator;
    }


    /**
     * @param GuardEvent $event
     */
    public function guardReady(GuardEvent $event): void
    {
        /** @var Order $order */
        $order = $event->getSubject();
        if (!$this->orderValidator->validateStock($order) && !$this->orderValidator->forceExportation($order)) {
            $event->addTransitionBlocker(new TransitionBlocker('Order not ready and not forced' , 0));
        }
    }

    /**
     * @param GuardEvent $event
     */
    public function guardDelivered(GuardEvent $event): void
    {
        /** @var Order $order */
        $order = $event->getSubject();
        if ($order->getDeliveryNote() === null) {
            $event->addTransitionBlocker(new TransitionBlocker('Delivery note not received' , 0));
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