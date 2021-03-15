<?php

/**
 * Author Thomas Beauchataud
 * Since 14/03/2021
 */


namespace App\Messenger\Order;


use App\Entity\OrderRow;
use App\Workflow\WorkflowRunner;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Handle the importation of a new order received from a transport by the order message
 */
class ImportOrderMessageHandler implements MessageHandlerInterface
{

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * @var WorkflowRunner
     */
    protected WorkflowRunner $workflow;

    /**
     * @var ValidatorInterface
     */
    protected ValidatorInterface $validator;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * ImportOrderMessageHandler constructor.
     * @param EntityManagerInterface $em
     * @param WorkflowRunner $workflow
     * @param ValidatorInterface $validator
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManagerInterface $em,
                                WorkflowRunner $workflow,
                                ValidatorInterface $validator,
                                LoggerInterface $logger
    )
    {
        $this->em = $em;
        $this->workflow = $workflow;
        $this->validator = $validator;
        $this->logger = $logger;
    }


    /**
     * @param ImportOrderMessage $orderMessage
     */
    public function __invoke(ImportOrderMessage $orderMessage): void
    {
        try {
            $order = $orderMessage->getOrder();
            $constraintViolationList = $this->validator->validate($order);
            if ($constraintViolationList->has(0)) {
                throw new Exception($constraintViolationList->get(0));
            }
            $this->em->persist($order);
            $this->em->flush();
            $this->workflow->proceedOrder($order);
            /** @var OrderRow $orderRow */
            foreach($order->getOrderRows() as $orderRow) {
                $preparation = $orderRow->getPreparation();
                if($preparation !== null) {
                    $this->workflow->proceedPreparation($preparation);
                }
            }
        } catch (Exception $e) {
            $this->logger->alert("Unable to import the order from"); //TODO ADD ORDER CONTEXT
        }
    }

}