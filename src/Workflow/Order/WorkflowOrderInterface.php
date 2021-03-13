<?php


namespace App\Workflow\Order;


use App\Entity\WorkflowOrder;

interface WorkflowOrderInterface
{

    /**
     * @param WorkflowOrder $workflowOrder
     */
    public function proceed(WorkflowOrder $workflowOrder): void;

}