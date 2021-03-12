<?php


namespace App\Service;


use App\Entity\OrderRow;
use App\Entity\WorkflowOrder;

class OrderExporter implements OrderExporterInterface
{

    /**
     * @inheritDoc
     */
    public function exportOrder(WorkflowOrder $workflowOrder): void
    {
        $file = fopen('orders.csv', 'w');
        /** @var OrderRow $orderRow */
        foreach($workflowOrder->getOrderRows() as $orderRow) {
            fwrite($file, $orderRow->getSerialization());
        }
        fclose($file);
    }

}