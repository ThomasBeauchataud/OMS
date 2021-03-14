<?php

/**
 * Author Thomas Beauchataud
 * From 14/03/2021
 */


namespace App\Messenger\Order;


use App\Workflow\RunnerWorkflow;
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
     * @var RunnerWorkflow
     */
    protected RunnerWorkflow $workflow;

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
     * @param RunnerWorkflow $workflow
     * @param ValidatorInterface $validator
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManagerInterface $em,
                                RunnerWorkflow $workflow,
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
        } catch (Exception $e) {
            $this->logger->alert("Unable to import the order from"); //TODO ADD ORDER CONTEXT
        }
    }

}