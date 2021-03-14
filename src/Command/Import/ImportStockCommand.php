<?php

/**
 * Author Thomas Beauchataud
 * From 14/03/2021
 */


namespace App\Command\Import;


use App\Entity\Entity;
use App\Entity\Sender;
use App\Entity\Stock;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Workflow\WorkflowInterface;

class ImportStockCommand extends Command
{

    public const FILE_NAME = 'import.csv';

    /**
     * @inheritdoc
     */
    protected static $defaultName = "app:import-stock";

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
        $baseFolder = $this->parameterBag->get('import.stock.folder');
        foreach (scandir($baseFolder) as $senderAlias) {
            /** @var Sender $sender */
            $sender = $this->em->getRepository(Sender::class)->findOneBy(['alias' => $senderAlias]);
            if ($sender !== null) {
                foreach (scandir("$baseFolder/$senderAlias") as $entityAlias) {
                    /** @var Entity $entity */
                    $entity = $this->em->getRepository(Entity::class)->findOneBy(['alias' => $entityAlias]);
                    if ($entity !== null) {
                        foreach (scandir("$baseFolder/$senderAlias/$entityAlias") as $file) {
                            if ($file === self::FILE_NAME) {
                                $this->em->getRepository(Stock::class)->removeFromSenderEntity($sender, $entity);
                                $this->createStocks($sender, $entity, "$baseFolder/$senderAlias/$entityAlias/$file");
                                $this->em->flush();
                            }
                        }
                    }
                }
            }
        }
        return self::SUCCESS;
    }

    /**
     * @param Sender $sender
     * @param Entity $entity
     * @param string $filePath
     */
    private function createStocks(Sender $sender, Entity $entity, string $filePath): void
    {
        var_dump($sender->getAlias());
        var_dump($filePath);
        ini_set('auto_detect_line_endings', TRUE);
        $handle = fopen($filePath, 'r');
        $header = true;
        while (($data = fgetcsv($handle, 0, ';')) !== FALSE) {
            if ($header) {
                $header = false;
                continue;
            }
            if ($sender->getAlias() === 'dp_epone') {
                $quantity = intval($data[11]);
            } else {
                $quantity = intval($data[10]);
            }
            if ($quantity === 0) {
                continue;
            }
            $stock = new Stock();
            $stock->setEntity($entity);
            $stock->setSender($sender);
            $stock->setQuantity($quantity);
            $stock->setProduct($data[0]);
            $this->em->persist($stock);
        }
        ini_set('auto_detect_line_endings', FALSE);
    }

}