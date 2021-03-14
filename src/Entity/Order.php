<?php

/**
 * Author Thomas Beauchataud
 * From 14/03/2021
 */


namespace App\Entity;


use App\Repository\OrderRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{

    /**
     * The id of the order defined by the OMS
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /**
     * The id defined to by transmitter of the order, used to communicate with the transmitter about the order
     *
     * @ORM\Column(type="integer")
     * @Assert\NotNull(message="Missing externalId attribute")
     */
    protected int $externalId;

    /**
     * The transmitter who sent the order to this OMS
     *
     * @ORM\ManyToOne(targetEntity=Transmitter::class, inversedBy="order", fetch="EAGER")
     * @Assert\NotNull(message="Missing transmitter attribute")
     */
    protected Transmitter $transmitter;

    /**
     * The defined sender of the order, who is defined at the initialisation of the workflow
     *
     * @ORM\ManyToOne(targetEntity=Sender::class, inversedBy="order", fetch="EAGER")
     */
    protected ?Sender $sender;

    /**
     * The delivery note received from the sender of the order when the order is delivered
     *
     * @ORM\OneToOne(targetEntity=DeliveryNote::class, fetch="EAGER", cascade={"persist", "remove"})
     */
    protected ?DeliveryNote $deliveryNote;

    /**
     * The order rows of the order containing product with their quantities ordered
     *
     * @ORM\OneToMany(targetEntity=OrderRow::class, mappedBy="order", fetch="EAGER", cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    protected Collection $orderRows;


    /*****************************************
     *****************************************
     ********** WORKFLOW ATTRIBUTES **********
     *****************************************
     *****************************************/


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
     * @ORM\Column(type="boolean")
     */
    protected bool $forcedIncomplete;


    /*****************************************
     *****************************************
     ************** CONSTRUCTOR **************
     *****************************************
     *****************************************/


    /**
     * Order constructor.
     */
    public function __construct()
    {
        $this->closed = false;
        $this->forcedIncomplete = false;
        $this->state = array();
        $this->lastUpdate = new DateTime();
        $this->sender = null;
        $this->deliveryNote = null;
        $this->orderRows = new ArrayCollection();
    }


    /*****************************************
     *****************************************
     ********* ATTRIBUTES ACCESSORS **********
     *****************************************
     *****************************************/


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getExternalId(): int
    {
        return $this->externalId;
    }

    /**
     * @param int $externalId
     */
    public function setExternalId(int $externalId): void
    {
        $this->externalId = $externalId;
    }

    /**
     * @return Transmitter
     */
    public function getTransmitter(): Transmitter
    {
        return $this->transmitter;
    }

    /**
     * @param Transmitter $transmitter
     */
    public function setTransmitter(Transmitter $transmitter): void
    {
        $this->transmitter = $transmitter;
    }

    /**
     * @return Sender|null
     */
    public function getSender(): ?Sender
    {
        return $this->sender;
    }

    /**
     * @param Sender $sender
     */
    public function setSender(Sender $sender): void
    {
        $this->sender = $sender;
    }

    /**
     * @return bool
     */
    public function hasDeliveryNote(): bool
    {
        return $this->deliveryNote !== null;
    }

    /**
     * @return DeliveryNote|null
     */
    public function getDeliveryNote(): ?DeliveryNote
    {
        return $this->deliveryNote;
    }

    /**
     * @param DeliveryNote|null $deliveryNote
     */
    public function setDeliveryNote(?DeliveryNote $deliveryNote): void
    {
        $this->deliveryNote = $deliveryNote;
    }

    /**
     * @return Collection
     */
    public function getOrderRows(): Collection
    {
        return $this->orderRows;
    }

    /**
     * @param Collection|array $orderRows
     */
    public function setOrderRows($orderRows): void
    {
        foreach ($orderRows as $orderRow) {
            $this->addOrderRow($orderRow);
        }
    }

    /**
     * @param OrderRow $orderRow
     */
    public function addOrderRow(OrderRow $orderRow): void
    {
        if (!$this->orderRows->contains($orderRow)) {
            $this->orderRows->add($orderRow);
            $orderRow->setOrder($this);
        }
    }


    /*****************************************
     *****************************************
     ********** WORKFLOW ACCESSORS ***********
     *****************************************
     *****************************************/


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
     * @return DateTimeInterface
     */
    public function getLastUpdate(): DateTimeInterface
    {
        return $this->lastUpdate;
    }


    /*****************************************
     *****************************************
     **************** METHODS ****************
     *****************************************
     *****************************************/


    /**
     * @return bool
     */
    public function containsMedicine(): bool
    {
        /** @var OrderRow $orderRow */
        foreach ($this->orderRows as $orderRow) {
            if ($orderRow->isMedicine()) {
                return true;
            }
        }
        return false;
    }


    /*****************************************
     *****************************************
     *********** WORKFLOW METHODS ************
     *****************************************
     *****************************************/


    /**
     * Returns true when the order has to be exported to sender no matter what
     *
     * @return bool
     */
    public function forceReadyState(): bool
    {
        $now = new DateTime();
        return $now->diff($this->getLastUpdate())->h > 36;
    }

}
