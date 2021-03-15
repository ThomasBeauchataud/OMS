<?php

/**
 * Author Thomas Beauchataud
 * Since 14/03/2021
 */


namespace App\Workflow\Preparation;


use App\Entity\Preparation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * TODO Create retrocession state for preparation whose the picker entity is different from the transmitter entity of the order
 */
class PreparationWorkflowSubscriber implements EventSubscriberInterface
{

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * @var PreparationWorkflowServiceInterface
     */
    protected PreparationWorkflowServiceInterface $workflowService;

    /**
     * @var WorkflowInterface
     */
    protected WorkflowInterface $workflow;

    /**
     * OrderWorkflowSubscriber constructor.
     * @param EntityManagerInterface $em
     * @param PreparationWorkflowServiceInterface $workflowService
     * @param WorkflowInterface $preparationWorkflow
     */
    public function __construct(EntityManagerInterface $em,
                                PreparationWorkflowServiceInterface $workflowService,
                                WorkflowInterface $preparationWorkflow
    )
    {
        $this->em = $em;
        $this->workflowService = $workflowService;
        $this->workflow = $preparationWorkflow;
    }

    /**
     * Setup the sender of the order
     *
     * @param Event $event
     */
    public function export(Event $event): void
    {
        /** @var Preparation $preparation */
        $preparation = $event->getSubject();
        $this->workflowService->exportToPicker($preparation);
        $this->workflowService->updateRealStock($preparation);
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
            'workflow.preparation.entered.received' => 'onReceived',
            'workflow.preparation.enter.closed' => 'close',
            'workflow.preparation.entered.closed' => 'onClosed',
        );
    }

}