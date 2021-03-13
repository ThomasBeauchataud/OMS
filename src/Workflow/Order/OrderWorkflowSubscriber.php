<?php


namespace App\Workflow\Order;


use App\Entity\Order;
use App\Service\PreparationFactory;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use LogicException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\WorkflowInterface;

class OrderWorkflowSubscriber implements EventSubscriberInterface
{

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * @var OrderExporterInterface
     */
    protected OrderExporterInterface $orderExporter;

    /**
     * @var SenderSelectorInterface
     */
    protected SenderSelectorInterface $senderSelector;

    /**
     * @var PreparationFactory
     */
    protected PreparationFactory $preparationFactory;

    /**
     * @var OrderValidatorInterface
     */
    protected OrderValidatorInterface $orderValidator;

    /**
     * @var WorkflowInterface
     */
    protected WorkflowInterface $workflow;

    /**
     * OrderWorkflowSubscriber constructor.
     * @param EntityManagerInterface $em
     * @param OrderExporterInterface $orderExporter
     * @param SenderSelectorInterface $senderSelector
     * @param WorkflowInterface $orderWorkflow
     * @param PreparationFactory $preparationCreator
     * @param OrderValidatorInterface $orderValidator
     */
    public function __construct(EntityManagerInterface $em,
                                OrderExporterInterface $orderExporter,
                                SenderSelectorInterface $senderSelector,
                                WorkflowInterface $orderWorkflow,
                                PreparationFactory $preparationCreator,
                                OrderValidatorInterface $orderValidator
    )
    {
        $this->em = $em;
        $this->orderExporter = $orderExporter;
        $this->senderSelector = $senderSelector;
        $this->workflow = $orderWorkflow;
        $this->preparationFactory = $preparationCreator;
        $this->orderValidator = $orderValidator;
    }

    /**
     * Setup the sender of the order
     *
     * @param Event $event
     */
    public function initialize(Event $event): void
    {
        /** @var Order $order */
        $order = $event->getSubject();
        if ($order->getSender() === null) {
            $sender = $this->senderSelector->selectSenderForOrder($order);
            $order->setSender($sender);
        }
        $this->em->getRepository(Order::class)->updateSender($order);
    }

    /**
     * Save the entity
     * Pass the order to the state _preparation_
     *
     * @param Event $event
     */
    public function onInitialized(Event $event): void
    {
        /** @var Order $order */
        $order = $event->getSubject();
        if ($order->getSender() === null) {
            throw new LogicException("An initialized order should have a defined sender.");
            //TODO LOG AND INJECT THE ORDER AT THE PREVIOUS STATE
        }
        /** @var Order $order */
        $order = $event->getSubject();
        $this->em->getRepository(Order::class)->updateState($order);
    }

    /**
     * Save the entity
     * Check if the order can pass to the state _ready_, it not, generate preparation
     *
     * @param Event $event
     * @throws Exception
     */
    public function onPreparation(Event $event): void
    {
        /** @var Order $order */
        $order = $event->getSubject();
        $transitions = $this->workflow->getEnabledTransitions($order);
        foreach ($transitions as $transition) {
            if ($this->workflow->can($order, $transition->getName())) {
                return;
            }
        }
        if (!$this->orderValidator->hasOrderPreparationInProgress($order)) {
            if(!$this->preparationFactory->createFromWorkflowOrder($order)) {
                $order->setForcedIncomplete(true);
            }
        }
        $this->em->getRepository(Order::class)->updateStateAndIncomplete($order);
    }

    /**
     * Save the entity
     * Pass the order to the state _exported_
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
        $this->orderExporter->exportToSender($order);
    }

    /**
     * Save the entity
     * If possible ass the order to the state _delivered_
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
        $this->orderExporter->exportToTransmitter($order);
    }

    /**
     * Save the entity
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
     * Set the entity in closed state
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
     * Save the entity
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