<?php


namespace App\Order\Entity;


use App\Order\Repository\OrderRowRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=OrderRowRepository::class)
 */
class OrderRow
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
     * @ORM\Column(type="integer")
     * @Groups({"order"})
     */
    private int $quantity;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="order")
     */
    private Order $order;

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
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @param Order $order
     */
    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

}
