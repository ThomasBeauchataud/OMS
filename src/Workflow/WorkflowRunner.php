<?php


namespace App\Workflow;


use App\Entity\WorkflowOrder;
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
    public function proceed(WorkflowOrder $workflowOrder): void
    {
        $workflow = $this->workflowRegistry->get($workflowOrder);
        $transitions = $workflow->getEnabledTransitions($workflowOrder);
        /** @var Transition $transition */
        foreach ($transitions as $transition) {
            if ($workflow->can($workflowOrder, $transition->getName())) {
                $workflow->apply($workflowOrder, $transition->getName());
                break;
            }

        }
    }

}