<?php

/**
 * Author Thomas Beauchataud
 * From 14/03/2021
 */


namespace App\Messenger\DeliveryNote;


use App\Workflow\RunnerWorkflow;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Handle the importation of a new order received from a transport by the order message
 */
class ImportDeliveryNoteMessageHandler implements MessageHandlerInterface
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
     * OrderMessageHandler constructor.
     * @param EntityManagerInterface $em
     * @param RunnerWorkflow $workflow
     * @param ValidatorInterface $validator
     */
    public function __construct(EntityManagerInterface $em, RunnerWorkflow $workflow, ValidatorInterface $validator)
    {
        $this->em = $em;
        $this->workflow = $workflow;
        $this->validator = $validator;
    }


    /**
     * @param ImportDeliveryNoteMessage $deliveryNoteMessage
     */
    public function __invoke(ImportDeliveryNoteMessage $deliveryNoteMessage): void
    {
        try {
            $deliveryNote = $deliveryNoteMessage->getDeliveryNote();
            $constraintViolationList = $this->validator->validate($deliveryNote);
            if ($constraintViolationList->has(0)) {
                throw new Exception($constraintViolationList->get(0));
            }
            $this->em->persist($deliveryNote);
            $this->em->flush();
            //$this->workflow->proceedOrder($order);
        } catch (Exception $e) {
            var_dump($e->getMessage());
        }
    }

}