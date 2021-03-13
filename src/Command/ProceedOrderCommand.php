<?php


namespace App\Command;


use App\Entity\Order;
use App\Workflow\Order\WorkflowOrderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProceedOrderCommand extends Command
{

    /**
     * @inheritdoc
     */
    protected static $defaultName = "app:proceed";

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * @var WorkflowOrderInterface
     */
    protected WorkflowOrderInterface $workflow;

    /**
     * OrderLoaderCommand constructor.
     * @param EntityManagerInterface $em
     * @param WorkflowOrderInterface $orderWorkflow
     */
    public function __construct(EntityManagerInterface $em, WorkflowOrderInterface $orderWorkflow)
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
            $this->workflow->proceed($order);
            $percent = intval(($count / $total) * 100);
            $output->write("\r$percent%");
        }
        return self::SUCCESS;
    }

}