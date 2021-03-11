<?php


namespace App\Order\Messenger;


use App\Order\Entity\Order;
use App\Order\Service\OrderTransporterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class OrderHandler implements MessageSubscriberInterface
{

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * @var OrderTransporterInterface
     */
    protected OrderTransporterInterface $orderTransporter;

    /**
     * OrderHandler constructor.
     * @param EntityManagerInterface $em
     * @param OrderTransporterInterface $orderTransporter
     */
    public function __construct(EntityManagerInterface $em, OrderTransporterInterface $orderTransporter)
    {
        $this->em = $em;
        $this->orderTransporter = $orderTransporter;
    }


    /**
     * Disptach an order to the send
     *
     * @param DispatchOrderMessage $message
     */
    public function handleDispatchOrderMessage(DispatchOrderMessage $message)
    {
        $order = $message->getOrder();
        $this->orderTransporter->transport($order);
        $order->setState(Order::STATE_SENT);
        $this->em->persist($order);
        $this->em->flush();
    }

    /**
     * @inheritdoc
     */
    public static function getHandledMessages(): iterable
    {
        yield DispatchOrderMessage::class => ["method" => "handleDispatchOrderMessage"];
    }

}