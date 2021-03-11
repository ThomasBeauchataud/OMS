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

class OrderLoaderCommand extends Command
{

    /**
     * @inheritdoc
     */
    protected static $defaultName = "app:order:load";

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
        foreach (scandir('data') as $folder) {
            /** @var Transmitter $transmitter */
            $transmitter = $this->em->getRepository(Transmitter::class)->findOneBy(['folder' => $folder]);
            if ($transmitter !== null) {
                foreach (scandir("data/$folder") as $file) {
                    if (!str_contains($file, '.csv')) {
                        continue;
                    }
                    $orders = array();
                    ini_set('auto_detect_line_endings', TRUE);
                    $handle = fopen("data/$folder/$file", 'r');
                    while (($data = fgetcsv($handle, 0, ';')) !== FALSE) {
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
                            $orderRow->setExternalId(intval($or[1]));
                            $orderRow->setQuantity(intval($or[3]));
                            $order->addOrderRow($orderRow);
                        }
                        $this->workflow->apply($order, 'to_initialized');
                        $this->em->persist($order);
                    }
                }
            }
        }
        $this->em->flush();
        return self::SUCCESS;
    }

}