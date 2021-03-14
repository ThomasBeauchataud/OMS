<?php

/**
 * Author Thomas Beauchataud
 * From 14/03/2021
 */


namespace App\Command\Import;


use App\Entity\Transmitter;
use App\Entity\Order;
use App\Entity\OrderRow;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Workflow\WorkflowInterface;

class ImportOrderCommand extends Command
{

    /**
     * @inheritdoc
     */
    protected static $defaultName = "app:import-order";

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * @var WorkflowInterface
     */
    protected WorkflowInterface $workflow;

    /**
     * @var ParameterBagInterface
     */
    protected ParameterBagInterface $parameterBag;

    /**
     * OrderLoaderCommand constructor.
     * @param EntityManagerInterface $em
     * @param WorkflowInterface $orderWorkflow
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(EntityManagerInterface $em, WorkflowInterface $orderWorkflow, ParameterBagInterface $parameterBag)
    {
        parent::__construct();
        $this->em = $em;
        $this->workflow = $orderWorkflow;
        $this->parameterBag = $parameterBag;
    }


    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $baseFolder = $this->parameterBag->get('import.order.folder');
        foreach (scandir($baseFolder) as $folder) {
            /** @var Transmitter $transmitter */
            $transmitter = $this->em->getRepository(Transmitter::class)->findOneBy(['alias' => $folder]);
            if ($transmitter !== null) {
                foreach (scandir("$baseFolder/$folder") as $file) {
                    if (!str_contains($file, '.csv')) {
                        continue;
                    }
                    $orders = array();
                    ini_set('auto_detect_line_endings', TRUE);
                    $handle = fopen("$baseFolder/$folder/$file", 'r');
                    $header = true;
                    while (($data = fgetcsv($handle, 0, ';')) !== FALSE) {
                        if ($header) {
                            $header = false;
                            continue;
                        }
                        $orders[$data[0]][] = $data;
                    }
                    ini_set('auto_detect_line_endings', FALSE);
                    foreach($orders as $orderId => $o) {
                        $order = new Order();
                        $order->setTransmitter($transmitter);
                        $order->setExternalId(intval($orderId));
                        foreach($o as $or) {
                            $orderRow = new OrderRow();
                            $orderRow->setQuantity(intval($or[2]));
                            $orderRow->setProduct($or[74]);
                            $orderRow->setEan($or[73]);
                            $orderRow->setSerialization(implode(";", $or));
                            $order->addOrderRow($orderRow);
                        }
                        $this->em->persist($order);
                    }
                }
            }
        }
        $this->em->flush();
        return self::SUCCESS;
    }

}