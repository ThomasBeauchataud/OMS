<?php


namespace App\Workflow;


use App\Entity\Order;
use App\Workflow\Order\WorkflowOrderInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Transition;

class WorkflowRunner implements WorkflowOrderInterface
{

    /**
     * @var Registry
     */
    protected Registry $workflowRegistry;

    /**
     * WorkflowRunner constructor.
     * @param Registry $workflowRegistry
     */
    public function __construct(Registry $workflowRegistry)
    {
        $this->workflowRegistry = $workflowRegistry;
    }


    /**
     * @inheritDoc
     */
    public function proceed(Order $order): void
    {
        $workflow = $this->workflowRegistry->get($order);
        $transitions = $workflow->getEnabledTransitions($order);
        /** @var Transition $transition */
        foreach ($transitions as $transition) {
            if ($workflow->can($order, $transition->getName())) {
                $workflow->apply($order, $transition->getName());
                break;
            }

        }
    }

}