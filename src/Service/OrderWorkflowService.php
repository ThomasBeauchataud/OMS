<?php

/**
 * Author Thomas Beauchataud
 * From 14/03/2021
 */


namespace App\Service;


use App\Entity\OrderRow;
use App\Entity\Order;
use App\Entity\Preparation;
use App\Entity\Sender;
use App\Entity\TransmitterSender;
use App\Workflow\Order\OrderWorkflowServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class OrderWorkflowService implements OrderWorkflowServiceInterface
{

    /**
     * @var ParameterBagInterface
     */
    protected ParameterBagInterface $parameterBag;

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * @var PreparationFactory
     */
    protected PreparationFactory $preparationFactory;

    /**
     * OrderWorkflowService constructor.
     * @param ParameterBagInterface $parameterBag
     * @param EntityManagerInterface $em
     * @param PreparationFactory $preparationFactory
     */
    public function __construct(ParameterBagInterface $parameterBag, EntityManagerInterface $em, PreparationFactory $preparationFactory)
    {
        $this->parameterBag = $parameterBag;
        $this->em = $em;
        $this->preparationFactory = $preparationFactory;
    }


    /**
     * @inheritDoc
     */
    public function exportToSender(Order $order): void
    {
        $directoryPath = $this->parameterBag->get('export.order.folder') . "\\" . $order->getSender()->getAlias();
        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 777, true);
        }
        $filePath = $directoryPath . "\\" . $this->parameterBag->get('export.order.sender.file');
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
        $filePath = $this->parameterBag->get('export.order.folder') . "\\" . $order->getTransmitter()->getAlias() . $this->parameterBag->get('export.order.transmitter.file');
        $file = fopen($filePath, 'w');
        /** @var OrderRow $orderRow */
        foreach($order->getOrderRows() as $orderRow) {
            fwrite($file, $orderRow->getSerialization());
        }
        fclose($file);
    }

    /**
     * First filter sender depending on if the order contains medicine and if the sender can manage medicine orders
     * Finally select the sender by the defined priority
     *
     * @inheritDoc
     */
    public function selectSenderForOrder(Order $order): Sender
    {
        $transmitterSenders = iterator_to_array($order->getTransmitter()->getTransmitterSenders());
        if ($order->containsMedicine()) {
            $transmitterSenders = array_filter($transmitterSenders, function (TransmitterSender $transmitterSender) {
                return $transmitterSender->getSender()->isMedicineManager();
            });
        }
        usort($transmitterSenders, function (TransmitterSender $ts1, TransmitterSender $ts2) {
            return $ts1->getPriority() <=> $ts2->getPriority();
        });
        return array_shift($transmitterSenders)->getSender();
    }

    /**
     * @inheritDoc
     */
    public function createNeededPreparation(Order $order): void
    {
        $preparations = $this->preparationFactory->create($order);
        if (count($preparations) > 0) {
            $this->em->getRepository(Preparation::class)->insertMultiple($preparations);
        }
    }
}