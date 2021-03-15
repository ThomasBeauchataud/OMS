<?php

/**
 * Author Thomas Beauchataud
 * Since 14/03/2021
 */


namespace App\Workflow\Preparation;


use App\Entity\Order;
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
     * @inheritDoc
     */
    public
    static function getSubscribedEvents(): array
    {
        return array(
            'workflow.preparation.guard.to_sent' => 'guardSent',
        );
    }

}