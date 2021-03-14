<?php

/**
 * Author Thomas Beauchataud
 * From 14/03/2021
 */


namespace App\Workflow\Preparation;


use App\Entity\Order;
use App\Entity\Preparation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\WorkflowInterface;

class PreparationWorkflowSubscriber implements EventSubscriberInterface
{

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * @var PreparationWorkflowServiceInterface
     */
    protected PreparationWorkflowServiceInterface $preparationExporter;

    /**
     * @var WorkflowInterface
     */
    protected WorkflowInterface $workflow;

    /**
     * OrderWorkflowSubscriber constructor.
     * @param EntityManagerInterface $em
     * @param PreparationWorkflowServiceInterface $preparationExporter
     * @param WorkflowInterface $preparationWorkflow
     */
    public function __construct(EntityManagerInterface $em,
                                PreparationWorkflowServiceInterface $preparationExporter,
                                WorkflowInterface $preparationWorkflow
    )
    {
        $this->em = $em;
        $this->preparationExporter = $preparationExporter;
        $this->workflow = $preparationWorkflow;
    }

    /**
     * Setup the sender of the order
     *
     * @param Event $event
     */
    public function export(Event $event): void
    {
        /** @var WorkflowPreparation $workflowPreparation */
        $workflowPreparation = $event->getSubject();
        //TODO EXPORT WORKFLOW
    }

    /**
     * Save the entity
     * Continue the workflow
     *
     * @param Event $event
     */
    public function onExported(Event $event): void
    {
        /** @var Preparation $preparation */
        $preparation = $event->getSubject();
        $this->em->getRepository(Preparation::class)->updateState($preparation);
    }

    /**
     * Save the entity
     * Continue the workflow
     *
     * @param Event $event
     */
    public function onSent(Event $event): void
    {
        /** @var Preparation $preparation */
        $preparation = $event->getSubject();
        $this->em->getRepository(Preparation::class)->updateState($preparation);
    }

    /**
     * Save the entity
     *
     * @param Event $event
     */
    public function onReceived(Event $event): void
    {
        /** @var Preparation $preparation */
        $preparation = $event->getSubject();
        $this->em->getRepository(Preparation::class)->updateState($preparation);
    }

    /**
     * Set the preparation as closed and save it
     *
     * @param Event $event
     */
    public function close(Event $event): void
    {
        /** @var Preparation $preparation */
        $preparation = $event->getSubject();
        $preparation->setClosed(true);
        $this->em->getRepository(Preparation::class)->updateClosed($preparation);
    }

    /**
     * Saved the preparation state
     *
     * @param Event $event
     */
    public function onClosed(Event $event): void
    {
        /** @var Preparation $preparation */
        $preparation = $event->getSubject();
        $this->em->getRepository(Preparation::class)->updateState($preparation);
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return array(
            'workflow.preparation.enter.exported' => 'export',
            'workflow.preparation.entered.exported' => 'onExported',
            'workflow.preparation.entered.sent' => 'onSent',
            'workflow.preparation.enter.received' => 'onReceived',
        );
    }

}