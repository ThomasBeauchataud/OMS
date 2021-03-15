<?php

/**
 * Author Thomas Beauchataud
 * Since 14/03/2021
 */


namespace App\Messenger\Order;


use App\Entity\OrderRow;
use App\Service\Order\OrderPersistentManager;
use App\Workflow\WorkflowRunner;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * Handle the importation of a new order received from a transport by the order message
 */
class ImportOrderMessageHandler implements MessageHandlerInterface
{

    /**
     * @var WorkflowRunner
     */
    protected WorkflowRunner $workflow;

    /**
     * @var OrderPersistentManager
     */
    protected OrderPersistentManager $orderPersistentManager;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * ImportOrderMessageHandler constructor.
     * @param WorkflowRunner $workflow
     * @param OrderPersistentManager $orderPersistentManager
     * @param LoggerInterface $logger
     */
    public function __construct(WorkflowRunner $workflow,
                                OrderPersistentManager $orderPersistentManager,
                                LoggerInterface $logger
    )
    {
        $this->workflow = $workflow;
        $this->orderPersistentManager = $orderPersistentManager;
        $this->logger = $logger;
    }


    /**
     * @param ImportOrderMessage $orderMessage
     */
    public function __invoke(ImportOrderMessage $orderMessage): void
    {
        $order = $orderMessage->getOrder();
        $errors = $this->orderPersistentManager->persist(array($order));
        $this->workflow->proceedOrder($order);
        /** @var OrderRow $orderRow */
        foreach ($order->getOrderRows() as $orderRow) {
            $preparation = $orderRow->getPreparation();
            if ($preparation !== null) {
                $this->workflow->proceedPreparation($preparation);
            }
        }
        foreach ($errors as $error) {
            $this->logger->alert(
                "Unable to import the order for the reason: " . $error->getReason(),
                $orderMessage->getContext()
            );
        }

    }

}