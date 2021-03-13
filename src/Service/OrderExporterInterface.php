<?php


namespace App\Service;


use App\Entity\WorkflowOrder;

interface OrderExporterInterface
{

    /**
     * @param WorkflowOrder $workflowOrder
     */
    public function exportToSender(WorkflowOrder $workflowOrder): void;

    /**
     * @param WorkflowOrder $workflowOrder
     */
    public function exportToTransmitter(WorkflowOrder $workflowOrder): void;

}