<?php

/**
 * Author Thomas Beauchataud
 * From 14/03/2021
 */


namespace App\Workflow;


use App\Entity\Order;
use App\Workflow\Order\OrderWorkflowInterface;
use Symfony\Component\Workflow\Registry;

class RunnerWorkflow implements OrderWorkflowInterface
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
        while(count($transitions = $workflow->getEnabledTransitions($order)) != 0) {
            $transition = array_shift($transitions);
            $workflow->apply($order, $transition->getName());
        }
    }

}