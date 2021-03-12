<?php


namespace App\Service;


use App\Entity\WorkflowOrder;

interface OrderExporterInterface
{

    /**
     * @param WorkflowOrder $workflowOrder
     */
    public function exportOrder(WorkflowOrder $workflowOrder): void;

}