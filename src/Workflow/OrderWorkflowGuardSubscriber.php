<?php


namespace App\Workflow;


use App\Entity\WorkflowOrder;
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
        /** @var WorkflowOrder $workflowOrder */
        $workflowOrder = $event->getSubject();
        if (!$this->orderValidator->validateStock($workflowOrder) && !$this->orderValidator->forceExportation($workflowOrder)) {
            $event->addTransitionBlocker(new TransitionBlocker('WorkflowOrder not ready and not forced' , 0));
        }
    }

    /**
     * @param GuardEvent $event
     */
    public function guardDelivered(GuardEvent $event): void
    {
        /** @var WorkflowOrder $workflowOrder */
        $workflowOrder = $event->getSubject();
        if (!$workflowOrder->hasDeliveryNote()) {
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