<?php

/**
 * Author Thomas Beauchataud
 * Since 14/03/2021
 */


namespace App\Service\Order;


use App\Entity\OrderRow;
use App\Entity\Order;
use App\Entity\Sender;
use App\Entity\TransmitterSender;
use App\Service\FileWriter;
use App\Service\Preparation\PreparationFactory;
use App\Service\Stock\StockManagerInterface;
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
     * @var StockManagerInterface
     */
    protected StockManagerInterface $stockManager;

    /**
     * OrderWorkflowService constructor.
     * @param ParameterBagInterface $parameterBag
     * @param EntityManagerInterface $em
     * @param PreparationFactory $preparationFactory
     * @param StockManagerInterface $stockManager
     */
    public function __construct(ParameterBagInterface $parameterBag,
                                EntityManagerInterface $em,
                                PreparationFactory $preparationFactory,
                                StockManagerInterface $stockManager
    )
    {
        $this->parameterBag = $parameterBag;
        $this->em = $em;
        $this->preparationFactory = $preparationFactory;
        $this->stockManager = $stockManager;
    }


    /**
     * @inheritDoc
     */
    public function exportToSender(Order $order): void
    {
        $directoryPath = $this->parameterBag->get('export.order.folder') . "\\" . $order->getSender()->getAlias();
        $fileName = $this->parameterBag->get('export.order.sender.file');
        $content = array_map(function (OrderRow $orderRow) {
            return $orderRow->getSerialization();
        }, iterator_to_array($order->getOrderRows()));
        FileWriter::writeFile($directoryPath, $fileName, $content);
    }

    /**
     * @inheritDoc
     */
    public function exportToTransmitter(Order $order): void
    {
        $directoryPath = $this->parameterBag->get('export.order.folder') . "\\" . $order->getTransmitter()->getAlias();
        $fileName = $this->parameterBag->get('export.order.transmitter.file');
        $content = array_map(function (OrderRow $orderRow) {
            return $orderRow->getSerialization();
        }, iterator_to_array($order->getOrderRows()));
        FileWriter::writeFile($directoryPath, $fileName, $content);
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
            foreach($preparations as $preparation) {
                $orderRow = $preparation->getOrderRow();
                $orderRow->setPreparation($preparation);
                $this->em->persist($orderRow);
            }
            $this->em->flush();
        }
    }

    /**
     * @inheritDoc
     */
    public function updateRealStock(Order $order): void
    {
        $this->stockManager->updateRealStocks(
            $order->getTransmitter()->getEntity(),
            $order->getSender(),
            array_map(function (OrderRow $orderRow) {
                return $orderRow->getProduct();
            }, iterator_to_array($order->getOrderRows()))
        );
    }
}