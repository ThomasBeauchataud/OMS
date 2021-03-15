<?php

/**
 * Author Thomas Beauchataud
 * From 15/03/2021
 */


namespace App\Service\Order;


use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OrderPersistentManager
{

    /**
     * @var ValidatorInterface
     */
    protected ValidatorInterface $validator;

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * OrderPersistentManager constructor.
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $em
     */
    public function __construct(ValidatorInterface $validator, EntityManagerInterface $em)
    {
        $this->validator = $validator;
        $this->em = $em;
    }


    /**
     * Valid and persist orders
     * Return invalidated orders with their cause
     *
     * @param array $orders
     * @return OrderPersistError[]
     */
    public function persist(array $orders): array
    {
        $errors = array();
        foreach($orders as $order) {
            if (!$order instanceof Order) {
                $errors[] = new OrderPersistError($order, "Exception order of type " . Order::class . ".");
                continue;
            }
            $constraintViolationList = $this->validator->validate($order);
            if ($constraintViolationList->has(0)) {
                $errors[] = new OrderPersistError($order, $constraintViolationList->get(0));
                continue;
            }
            $this->em->persist($order);
        }
        $this->em->flush();
        return $errors;
    }

}