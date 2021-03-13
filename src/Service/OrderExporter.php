<?php


namespace App\Service;


use App\Entity\OrderRow;
use App\Entity\Order;
use App\Workflow\Order\OrderExporterInterface;
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
    public function exportToSender(Order $order): void
    {
        $directoryPath = $this->exportFolder . "\\" . $order->getSender()->getAlias();
        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 777, true);
        }
        $filePath = $directoryPath . "\\" . $this->exportSenderFile;
        $file = fopen($filePath, 'w');
        /** @var OrderRow $orderRow */
        foreach($order->getOrderRows() as $orderRow) {
            fwrite($file, $orderRow->getSerialization());
        }
        fclose($file);
    }

    /**
     * @inheritDoc
     */
    public function exportToTransmitter(Order $order): void
    {
        $filePath = $this->exportFolder . "\\" . $order->getTransmitter()->getAlias() . $this->exportTransmitterFile;
        $file = fopen($filePath, 'w');
        /** @var OrderRow $orderRow */
        foreach($order->getOrderRows() as $orderRow) {
            fwrite($file, $orderRow->getSerialization());
        }
        fclose($file);
    }

}