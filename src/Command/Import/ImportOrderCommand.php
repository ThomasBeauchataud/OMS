<?php

/**
 * Author Thomas Beauchataud
 * Since 14/03/2021
 */


namespace App\Command\Import;


use App\Entity\Transmitter;
use App\Entity\Order;
use App\Entity\OrderRow;
use App\Service\Order\OrderPersistentManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

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
     * @var OrderPersistentManager
     */
    protected OrderPersistentManager $orderPersistentManager;

    /**
     * @var ParameterBagInterface
     */
    protected ParameterBagInterface $parameterBag;

    /**
     * ImportOrderCommand constructor.
     * @param EntityManagerInterface $em
     * @param OrderPersistentManager $orderPersistentManager
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(EntityManagerInterface $em,
                                OrderPersistentManager $orderPersistentManager,
                                ParameterBagInterface $parameterBag
    )
    {
        parent::__construct();
        $this->em = $em;
        $this->orderPersistentManager = $orderPersistentManager;
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
                    $finalOrders = array();
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
                        $finalOrders[] = $order;
                    }
                    $this->orderPersistentManager->persist($finalOrders);
                }
            }
        }
        return self::SUCCESS;
    }

}