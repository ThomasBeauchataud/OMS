<?php


namespace App\Entity;


use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass()
 */
abstract class WorkflowPreparation
{

    /**
     * @ORM\Column(type="json")
     */
    protected array $state;

    /**
     * @ORM\Column(type="datetime")
     */
    protected DateTimeInterface $lastUpdate;

    /**
     * WorkflowOrder constructor.
     */
    public function __construct()
    {
        $this->state = array();
        $this->lastUpdate = new DateTime();
    }

    /**
     * @return array
     */
    public function getState(): array
    {
        return $this->state;
    }

    /**
     * @param array $state
     */
    public function setState(array $state): void
    {
        if ($this->state !== $state) {
            $this->lastUpdate = new DateTime();
        }
        $this->state = $state;
    }

}