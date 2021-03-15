<?php

/**
 * Author Thomas Beauchataud
 * Since 14/03/2021
 */


namespace App\Workflow\Preparation;


use App\Entity\Preparation;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\TransitionBlocker;

/**
 * Prevent an preparation the pass to the next workflow state
 */
class PreparationWorkflowGuardSubscriber implements EventSubscriberInterface
{

    /**
     * Prevent a preparation to pass to the sent state while the picker didnt set the prepared quantity
     *
     * @param GuardEvent $event
     */
    public function guardSent(GuardEvent $event): void
    {
        /** @var Preparation $preparation */
        $preparation = $event->getSubject();
        if ($preparation->getSentQuantity() === null) {
            $event->addTransitionBlocker(new TransitionBlocker('Preparation still in progress', 0));
        }
    }

    /**
     * Prevent a preparation to pass to the closed state if there is retrocession order to created
     *
     * @param GuardEvent $event
     */
    public function guardClosed(GuardEvent $event): void
    {
        /** @var Preparation $preparation */
        $preparation = $event->getSubject();
        if ($preparation->isRetrocession() /** TODO && retrocession is done */) {
            $event->addTransitionBlocker(new TransitionBlocker('Preparation waiting for retrocession', 0));
        }
    }

    /**
     * @inheritDoc
     */
    public
    static function getSubscribedEvents(): array
    {
        return array(
            'workflow.preparation.guard.to_sent' => 'guardSent',
            'workflow.preparation.guard.to_closed' => 'guardClosed',
        );
    }

}