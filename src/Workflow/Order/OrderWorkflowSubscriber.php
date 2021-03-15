<?php

/**
 * Author Thomas Beauchataud
 * Since 14/03/2021
 */


namespace App\Workflow\Order;


use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

/**
 * Manage the workflow of an order
 */
class OrderWorkflowSubscriber implements EventSubscriberInterface
{

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * @var OrderWorkflowServiceInterface
     */
    protected OrderWorkflowServiceInterface $workflowService;

    /**
     * OrderWorkflowSubscriber constructor.
     * @param EntityManagerInterface $em
     * @param OrderWorkflowServiceInterface $workflowService
     */
    public function __construct(EntityManagerInterface $em,
                                OrderWorkflowServiceInterface $workflowService
    )
    {
        $this->em = $em;
        $this->workflowService = $workflowService;
    }

    /**
     * Setup the sender of the order if it is not and update the order in database
     *
     * @param Event $event
     */
    public function initialize(Event $event): void
    {
        /** @var Order $order */
        $order = $event->getSubject();
        if ($order->getSender() === null) {
            $sender = $this->workflowService->selectSenderForOrder($order);
            $order->setSender($sender);
        }
        $this->em->getRepository(Order::class)->updateSender($order);
        $this->workflowService->updateRealStock($order);
    }

    /**
     * Saved the order state
     *
     * @param Event $event
     */
    public function onInitialized(Event $event): void
    {
        /** @var Order $order */
        $order = $event->getSubject();
        $this->em->getRepository(Order::class)->updateState($order);
    }

    /**
     * Create preparation if needed, else orderRows have null preparation and order will pass to ready state
     *
     * @param Event $event
     */
    public function prepare(Event $event): void
    {
        /** @var Order $order */
        $order = $event->getSubject();
        $this->workflowService->createNeededPreparation($order);
    }

    /**
     * Saved the order state
     *
     * @param Event $event
     */
    public function onPreparation(Event $event): void
    {
        /** @var Order $order */
        $order = $event->getSubject();
        $this->em->getRepository(Order::class)->updateState($order);
    }

    /**
     * Saved the order state
     *
     * @param Event $event
     */
    public function onReady(Event $event): void
    {
        /** @var Order $order */
        $order = $event->getSubject();
        $this->em->getRepository(Order::class)->updateState($order);
    }

    /**
     * Export the order to the sender
     *
     * @param Event $event
     */
    public function export(Event $event): void
    {
        /** @var Order $order */
        $order = $event->getSubject();
        $this->workflowService->exportToSender($order);
    }

    /**
     * Saved the order state
     *
     * @param Event $event
     */
    public function onExported(Event $event): void
    {
        /** @var Order $order */
        $order = $event->getSubject();
        $this->em->getRepository(Order::class)->updateState($order);
    }

    /**
     * @param Event $event
     */
    public function deliver(Event $event): void
    {
        /** @var Order $order */
        $order = $event->getSubject();
        $this->workflowService->exportToTransmitter($order);
    }

    /**
     * Saved the order state
     *
     * @param Event $event
     */
    public function onDelivered(Event $event): void
    {
        /** @var Order $order */
        $order = $event->getSubject();
        $this->em->getRepository(Order::class)->updateState($order);
    }

    /**
     * Set the order as closed and save it
     *
     * @param Event $event
     */
    public function close(Event $event): void
    {
        /** @var Order $order */
        $order = $event->getSubject();
        $order->setClosed(true);
        $this->em->getRepository(Order::class)->updateClosed($order);
    }

    /**
     * Saved the order state
     *
     * @param Event $event
     */
    public function onClosed(Event $event): void
    {
        /** @var Order $order */
        $order = $event->getSubject();
        $this->em->getRepository(Order::class)->updateState($order);
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return array(
            'workflow.order.enter.initialized' => 'initialize',
            'workflow.order.entered.initialized' => 'onInitialized',
            'workflow.order.enter.preparation' => 'prepare',
            'workflow.order.entered.preparation' => 'onPreparation',
            'workflow.order.entered.ready' => 'onReady',
            'workflow.order.enter.exported' => 'export',
            'workflow.order.entered.exported' => 'onExported',
            'workflow.order.enter.delivered' => 'deliver',
            'workflow.order.entered.delivered' => 'onDelivered',
            'workflow.order.enter.closed' => 'close',
            'workflow.order.entered.closed' => 'onClosed'
        );
    }

}