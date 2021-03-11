<?php


namespace App\Order\Entity;


use App\Actor\Entity\Sender;
use App\Actor\Entity\Transmitter;
use App\Order\Repository\OrderRepository;
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

    public const STATE_CREATED = 0;
    public const STATE_GENERATE_PICKING = 1;
    public const STATE_WAITING_PICKING = 2;
    public const STATE_READY = 3;
    public const STATE_SENT = 4;

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
     * 0 = command created
     * 2 = command ready
     * 3 = command in preparation
     * 4 = command delivered
     *
     * @ORM\Column(type="integer")
     */
    private int $state;

    /**
     * @ORM\ManyToOne(targetEntity=Transmitter::class)
     */
    private Transmitter $transmitter;

    /**
     * @ORM\ManyToOne(targetEntity=Sender::class)
     */
    private ?Sender $sender;

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
        $this->state = 0;
        $this->sender = null;
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
     * @return int
     */
    public function getState(): int
    {
        return $this->state;
    }

    /**
     * @param int $state
     */
    public function setState(int $state): void
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
