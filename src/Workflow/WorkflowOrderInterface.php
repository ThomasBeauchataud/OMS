<?php


namespace App\Workflow;


use App\Entity\WorkflowOrder;

interface WorkflowOrderInterface
{

    /**
     * @param WorkflowOrder $workflowOrder
     */
    public function proceed(WorkflowOrder $workflowOrder): void;

}