<?php

/**
 * Author Thomas Beauchataud
 * From 14/03/2021
 */


namespace App\Workflow\Preparation;


use App\Entity\WorkflowPreparation;
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
     * @var PreparationExporterInterface
     */
    protected PreparationExporterInterface $preparationExporter;

    /**
     * @var WorkflowInterface
     */
    protected WorkflowInterface $workflow;

    /**
     * OrderWorkflowSubscriber constructor.
     * @param EntityManagerInterface $em
     * @param PreparationExporterInterface $preparationExporter
     * @param WorkflowInterface $preparationWorkflow
     */
    public function __construct(EntityManagerInterface $em,
                                PreparationExporterInterface $preparationExporter,
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
        $this->saveEvent($event);
        $this->continueWorkflow($event);
    }

    /**
     * Save the entity
     * Continue the workflow
     *
     * @param Event $event
     */
    public function onSent(Event $event): void
    {
        $this->saveEvent($event);
        $this->continueWorkflow($event);

    }

    /**
     * Save the entity
     *
     * @param Event $event
     */
    public function onDelivered(Event $event): void
    {
        $this->saveEvent($event);
    }

    /**
     * @param Event $event
     * @return bool
     */
    protected function continueWorkflow(Event $event): bool
    {
        $workflowPreparation = $event->getSubject();
        $transitions = $this->workflow->getEnabledTransitions($workflowPreparation);
        foreach ($transitions as $transition) {
            if ($this->workflow->can($workflowPreparation, $transition->getName())) {
                $this->workflow->apply($workflowPreparation, $transition->getName());
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
            'workflow.preparation.enter.exported' => 'export',
            'workflow.preparation.entered.exported' => 'onExported',
            'workflow.preparation.entered.sent' => 'onSent',
            'workflow.preparation.enter.received' => 'onReceived',
        );
    }

}