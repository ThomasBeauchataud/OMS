<?php


namespace App\Service;


use App\Entity\WorkflowOrder;

interface OrderValidatorInterface
{

    /**
     * @param WorkflowOrder $workflowOrder
     * @return bool
     */
    public function validateStock(WorkflowOrder $workflowOrder): bool;

    /**
     * @param WorkflowOrder $workflowOrder
     * @return bool
     */
    public function forceExportation(WorkflowOrder $workflowOrder): bool;

}