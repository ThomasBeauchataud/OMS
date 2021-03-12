<?php


namespace App\Service;


use App\Entity\WorkflowOrder;

interface OrderRendererInterface
{

    /**
     * @param WorkflowOrder $workflowOrder
     */
    public function render(WorkflowOrder $workflowOrder): void;

}