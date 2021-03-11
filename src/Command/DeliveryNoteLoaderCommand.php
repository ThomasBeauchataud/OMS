<?php


namespace App\Command;


use App\Entity\DeliveryNote;
use App\Entity\Transmitter;
use App\Entity\Order;
use App\Entity\OrderRow;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Workflow\WorkflowInterface;

class DeliveryNoteLoaderCommand extends Command
{

    /**
     * @inheritdoc
     */
    protected static $defaultName = "app:order:deliver";

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * OrderLoaderCommand constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }


    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var Order $order */
        $order = $this->em->getRepository(Order::class)->findOneBy(['closed' => false, 'deliveryNote' => null]);
        $order->setDeliveryNote(new DeliveryNote());
        $this->em->flush();
        return self::SUCCESS;
    }

}