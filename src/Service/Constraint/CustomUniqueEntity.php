<?php

/**
 * Author Thomas Beauchataud
 * From 15/03/2021
 */


namespace App\Service\Constraint;


use Symfony\Component\Validator\Constraint;
use Doctrine\Common\Annotations\Annotation;

/**
 * Add a constraint on an entity to validate his uniqueness
 * Put in parameters a combination of  fields that two entities can't have at the same time
 *
 * Example : ({"sender", "product"}) means that the two instance of an entity having attributes sender and product
 * can't have the same combination of sender and product
 *
 * @Annotation()
 */
class CustomUniqueEntity extends Constraint
{

    /**
     * @var array
     */
    public array $properties;

    /**
     * CustomUniqueEntity constructor.
     * @param array $properties
     */
    public function __construct(array $properties)
    {
        parent::__construct();
        $this->properties = $properties["value"];
    }


    /**
     * {@inheritdoc}
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

}