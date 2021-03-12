<?php


namespace App\Service;


use App\Entity\WorkflowOrder;

interface PreparationCreatorInterface
{

    /**
     * @param WorkflowOrder $workflowOrder
     */
    public function createPreparations(WorkflowOrder $workflowOrder): void;

}