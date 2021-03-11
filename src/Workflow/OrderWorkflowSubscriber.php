<?php


namespace App\Workflow;


use App\Entity\Order;
use App\Service\OrderExporterInterface;
use App\Service\OrderRendererInterface;
use App\Service\PreparationCreatorInterface;
use App\Service\SenderSelectorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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
     * @var OrderRendererInterface
     */
    protected OrderRendererInterface $orderRenderer;

    /**
     * @var SenderSelectorInterface
     */
    protected SenderSelectorInterface $senderSelector;

    /**
     * @var PreparationCreatorInterface
     */
    protected PreparationCreatorInterface $preparationCreator;

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
     * @param OrderRendererInterface $orderRenderer
     * @param PreparationCreatorInterface $preparationCreator
     */
    public function __construct(EntityManagerInterface $em,
                                OrderExporterInterface $orderExporter,
                                SenderSelectorInterface $senderSelector,
                                WorkflowInterface $orderWorkflow,
                                OrderRendererInterface $orderRenderer,
                                PreparationCreatorInterface $preparationCreator
    )
    {
        $this->em = $em;
        $this->orderExporter = $orderExporter;
        $this->senderSelector = $senderSelector;
        $this->workflow = $orderWorkflow;
        $this->orderRenderer = $orderRenderer;
        $this->preparationCreator = $preparationCreator;
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
            $sender = $this->senderSelector->selectSender($order);
            $order->setSender($sender);
        }
    }

    /**
     * Save the entity
     * Pass the order to the state _preparation_
     *
     * @param Event $event
     */
    public function onInitialized(Event $event): void
    {
        $this->saveEvent($event);
        $this->continueWorkflow($event);
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
        $this->saveEvent($event);
        if (!$this->continueWorkflow($event)) {
            /** @var Order $order */
            $order = $event->getSubject();
            $this->preparationCreator->createPreparations($order);
        }
    }

    /**
     * Save the entity
     * Pass the order to the state _exported_
     *
     * @param Event $event
     */
    public function onReady(Event $event): void
    {
        $this->saveEvent($event);
        $this->continueWorkflow($event);

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
        $this->orderExporter->exportOrder($order);
    }

    /**
     * Save the entity
     * If possible ass the order to the state _delivered_
     *
     * @param Event $event
     */
    public function onExported(Event $event): void
    {
        $this->saveEvent($event);
        $this->continueWorkflow($event);
    }

    /**
     * @param Event $event
     */
    public function deliver(Event $event): void
    {
        /** @var Order $order */
        $order = $event->getSubject();
        $this->orderRenderer->render($order);
    }

    /**
     * Save the entity
     *
     * @param Event $event
     */
    public function onDelivered(Event $event): void
    {
        $this->saveEvent($event);
        $this->continueWorkflow($event);
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
        $this->em->persist($order);
    }

    /**
     * Save the entity
     *
     * @param Event $event
     */
    public function onClosed(Event $event): void
    {
        $this->saveEvent($event);
    }

    /**
     * @param Event $event
     * @return bool
     */
    protected function continueWorkflow(Event $event): bool
    {
        $order = $event->getSubject();
        $transitions = $this->workflow->getEnabledTransitions($order);
        foreach ($transitions as $transition) {
            if ($this->workflow->can($order, $transition->getName())) {
                $this->workflow->apply($order, $transition->getName());
                return true;
            }
        }
        return false;
    }

    /**
     * @param Event $event
     */
    protected function saveEvent(Event $event)
    {
        $this->em->persist($event->getSubject());
        $this->em->flush();
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