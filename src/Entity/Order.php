<?php


namespace App\Entity;


use App\Repository\OrderRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"order"})
     */
    protected int $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"order"})
     */
    protected int $externalId;

    /**
     * @ORM\ManyToOne(targetEntity=Transmitter::class, fetch="EAGER")
     */
    protected Transmitter $transmitter;

    /**
     * @ORM\ManyToOne(targetEntity=Sender::class, fetch="EAGER")
     */
    protected ?Sender $sender;

    /**
     * @ORM\ManyToOne(targetEntity=DeliveryNote::class, cascade={"persist", "remove"})
     */
    protected ?DeliveryNote $deliveryNote;

    /**
     * @ORM\OneToMany(targetEntity=OrderRow::class, mappedBy="order", fetch="EAGER", cascade={"persist", "remove"})
     * @Groups({"order"})
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
     * @return DateTime|DateTimeInterface
     */
    public function getLastUpdate(): DateTime|DateTimeInterface
    {
        return $this->lastUpdate;
    }

    /**
     * @return bool
     */
    public function isForcedIncomplete(): bool
    {
        return $this->forcedIncomplete;
    }

    /**
     * @param bool $forcedIncomplete
     */
    public function setForcedIncomplete(bool $forcedIncomplete): void
    {
        $this->forcedIncomplete = $forcedIncomplete;
    }

}
