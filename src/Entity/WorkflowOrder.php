<?php


namespace App\Entity;


use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass()
 */
abstract class WorkflowOrder
{

    /**
     * @ORM\Column(type="boolean")
     */
    protected bool $closed;

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
        $this->closed = false;
        $this->state = array();
        $this->lastUpdate = new DateTime();
    }


    /**
     * @return bool
     */
    public function isClosed(): bool
    {
        return $this->closed;
    }

    /**
     * @param bool $closed
     */
    public function setClosed(bool $closed): void
    {
        $this->closed = $closed;
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

    /**
     * @return DateTime|DateTimeInterface
     */
    public function getLastUpdate(): DateTime|DateTimeInterface
    {
        return $this->lastUpdate;
    }

    /**
     * @return Sender|null
     */
    public abstract function getSender(): ?Sender;

    /**
     * @param Sender $sender
     */
    public abstract function setSender(Sender $sender): void;

    /**
     * @return bool
     */
    public abstract function hasDeliveryNote(): bool;

    /**
     * @return Collection
     */
    public abstract function getOrderRows(): Collection;

}