<?php

/**
 * Author Thomas Beauchataud
 * From 15/03/2021
 */


namespace App\Service\Constraint;


use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validate entities having the CustomUniqueEntity annotation
 */
class CustomUniqueEntityValidator extends ConstraintValidator
{

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * CustomUniqueEntityValidator constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint)
    {
        if(! $constraint instanceof CustomUniqueEntity) {
            throw new LogicException();
        }

        $parameters = array();
        foreach($constraint->properties as $property) {
            $methodName = "get" . ucwords($property);
            $parameters[$property] = call_user_func(array($value, $methodName));
        }

        $entityClass = get_class($value);

        $entity = $this->em->getRepository($entityClass)->findOneBy($parameters);

        if ($entity !== null) {
            $this->context->addViolation("Existing unique entity.");
        }
    }

}