<?php


namespace App\Command;


use App\Entity\Transmitter;
use App\Entity\Order;
use App\Entity\OrderRow;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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
     * OrderLoaderCommand constructor.
     * @param EntityManagerInterface $em
     * @param WorkflowInterface $orderWorkflow
     */
    public function __construct(EntityManagerInterface $em, WorkflowInterface $orderWorkflow)
    {
        parent::__construct();
        $this->em = $em;
        $this->workflow = $orderWorkflow;
    }


    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach (scandir('D:\\OMS\\data') as $folder) {
            /** @var Transmitter $transmitter */
            $transmitter = $this->em->getRepository(Transmitter::class)->findOneBy(['folder' => $folder]);
            if ($transmitter !== null) {
                foreach (scandir("D:\\OMS\\data/$folder") as $file) {
                    if (!str_contains($file, '.csv')) {
                        continue;
                    }
                    $orders = array();
                    ini_set('auto_detect_line_endings', TRUE);
                    $handle = fopen("D:\\OMS\\data/$folder/$file", 'r');
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
                            //TODO SET PRODUCT PATH 2
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