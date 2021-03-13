<?php


namespace App\Service;


use App\Entity\OrderRow;
use App\Entity\WorkflowOrder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class OrderExporter implements OrderExporterInterface
{

    /**
     * @var string
     */
    protected string $exportFolder;

    /**
     * @var string
     */
    protected string $exportSenderFile;

    /**
     * @var string
     */
    protected string $exportTransmitterFile;

    /**
     * OrderExporter constructor.
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->exportFolder = $parameterBag->get('export.order.folder');
        $this->exportSenderFile = $parameterBag->get('export.order.sender.file');
        $this->exportTransmitterFile = $parameterBag->get('export.order.transmitter.file');
    }


    /**
     * @inheritDoc
     */
    public function exportToSender(WorkflowOrder $workflowOrder): void
    {
        $filePath = $this->exportFolder . "\\" . $workflowOrder->getSender()->getFolder()  . $this->exportSenderFile;
        $file = fopen($filePath, 'w');
        /** @var OrderRow $orderRow */
        foreach($workflowOrder->getOrderRows() as $orderRow) {
            fwrite($file, $orderRow->getSerialization());
        }
        fclose($file);
    }

    /**
     * @inheritDoc
     */
    public function exportToTransmitter(WorkflowOrder $workflowOrder): void
    {
        $filePath = $this->exportFolder . "\\" . $workflowOrder->getTransmitter()->getFolder() . $this->exportTransmitterFile;
        $file = fopen($filePath, 'w');
        /** @var OrderRow $orderRow */
        foreach($workflowOrder->getOrderRows() as $orderRow) {
            fwrite($file, $orderRow->getSerialization());
        }
        fclose($file);
    }

}