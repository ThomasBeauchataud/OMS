<?php

/**
 * Author Thomas Beauchataud
 * From 14/03/2021
 */


namespace App\Workflow;


use App\Entity\Order;
use App\Entity\Preparation;
use Symfony\Component\Workflow\Registry;

class RunnerWorkflow
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
     * @param Order $order
     */
    public function proceedOrder(Order $order): void
    {
        $this->proceed($order);
    }

    /**
     * @param Preparation $preparation
     */
    public function proceedPreparation(Preparation $preparation): void
    {
        $this->proceed($preparation);
    }

    /**
     * @param $object
     */
    protected function proceed($object): void
    {
        $workflow = $this->workflowRegistry->get($object);
        while(count($transitions = $workflow->getEnabledTransitions($object)) != 0) {
            $transition = array_shift($transitions);
            $workflow->apply($object, $transition->getName());
        }
    }

}