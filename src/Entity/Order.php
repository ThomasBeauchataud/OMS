<?php


namespace App\Entity;


use App\Repository\OrderRepository;
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
    private int $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"order"})
     */
    private int $externalId;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $closed;

    /**
     * @ORM\Column(type="json")
     */
    private array $state;

    /**
     * @ORM\ManyToOne(targetEntity=Transmitter::class)
     */
    private Transmitter $transmitter;

    /**
     * @ORM\ManyToOne(targetEntity=Sender::class)
     */
    private ?Sender $sender;

    /**
     * @ORM\ManyToOne(targetEntity=DeliveryNote::class, cascade={"persist", "remove"})
     */
    private ?DeliveryNote $deliveryNote;

    /**
     * @ORM\OneToMany(targetEntity=OrderRow::class, mappedBy="order", cascade={"persist", "remove"})
     * @Groups({"order"})
     */
    private Collection $orderRows;

    /**
     * Order constructor.
     */
    public function __construct()
    {
        $this->closed = false;
        $this->state = array();
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
        $this->state = $state;
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

}
