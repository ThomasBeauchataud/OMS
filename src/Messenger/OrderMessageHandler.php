<?php

/**
 * Author Thomas Beauchataud
 * From 14/03/2021
 */


namespace App\Messenger;


use App\Workflow\Order\OrderWorkflowInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OrderMessageHandler implements MessageHandlerInterface
{

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * @var OrderWorkflowInterface
     */
    protected OrderWorkflowInterface $workflow;

    /**
     * @var ValidatorInterface
     */
    protected ValidatorInterface $validator;

    /**
     * OrderMessageHandler constructor.
     * @param EntityManagerInterface $em
     * @param OrderWorkflowInterface $workflow
     * @param ValidatorInterface $validator
     */
    public function __construct(EntityManagerInterface $em, OrderWorkflowInterface $workflow, ValidatorInterface $validator)
    {
        $this->em = $em;
        $this->workflow = $workflow;
        $this->validator = $validator;
    }


    /**
     * @param OrderMessage $orderMessage
     */
    public function __invoke(OrderMessage $orderMessage): void
    {
        try {
            $order = $orderMessage->getOrder();
            $constraintViolationList = $this->validator->validate($order);
            if ($constraintViolationList->has(0)) {
                throw new Exception($constraintViolationList->get(0));
            }
            if ($constraintViolationList)
            $this->em->persist($order);
            $this->em->flush();
            $this->workflow->proceed($order);
        } catch (Exception $e) {
            var_dump($e->getMessage());
        }
    }

}