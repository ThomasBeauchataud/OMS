<?php


namespace App\Service;


use App\Entity\Sender;
use App\Entity\WorkflowOrder;

interface SenderSelectorInterface
{

    /**
     * @param WorkflowOrder $workflowOrder
     * @return Sender
     */
    public function selectSender(WorkflowOrder $workflowOrder): Sender;

}