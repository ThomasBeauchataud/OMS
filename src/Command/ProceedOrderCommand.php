<?php

/**
 * Author Thomas Beauchataud
 * Since 14/03/2021
 */


namespace App\Command;


use App\Entity\Order;
use App\Workflow\WorkflowRunner;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProceedOrderCommand extends Command
{

    /**
     * @inheritdoc
     */
    protected static $defaultName = "app:proceed-order";

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * @var WorkflowRunner
     */
    protected WorkflowRunner $workflow;

    /**
     * OrderLoaderCommand constructor.
     * @param EntityManagerInterface $em
     * @param WorkflowRunner $orderWorkflow
     */
    public function __construct(EntityManagerInterface $em, WorkflowRunner $orderWorkflow)
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

        $orders = $this->em->getRepository(Order::class)->findBy(['closed' => false]);
        $total = count($orders);
        $output->writeln("Running workflow on $total commands.");
        $count = 0;
        /** @var Order $order */
        foreach($orders as $order) {
            $count += 1;
            $this->workflow->proceedOrder($order);
            $percent = intval(($count / $total) * 100);
            $output->write("\r$percent%");
        }
        return self::SUCCESS;
    }

}