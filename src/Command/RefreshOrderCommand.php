<?php


namespace App\Command;


use App\Entity\Order;
use App\Workflow\WorkflowOrderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshOrderCommand extends Command
{

    /**
     * @inheritdoc
     */
    protected static $defaultName = "app:order:refresh";

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
        /** @var Order $order */
        foreach($this->em->getRepository(Order::class)->findBy(['closed' => false]) as $order) {
            $this->workflow->proceed($order);
        }
        return self::SUCCESS;
    }

}
{

}