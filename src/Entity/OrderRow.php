<?php


namespace App\Entity;


use App\Repository\OrderRowRepository;
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
    private string $product;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"order"})
     */
    private string $ean;

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
     * @ORM\Column(type="string", length=65535)
     */
    private string $serialization;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getProduct(): string
    {
        return $this->product;
    }

    /**
     * @param string $product
     */
    public function setProduct(string $product): void
    {
        $this->product = $product;
    }

    /**
     * @return string
     */
    public function getEan(): string
    {
        return $this->ean;
    }

    /**
     * @param string $ean
     */
    public function setEan(string $ean): void
    {
        $this->ean = $ean;
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

    /**
     * @return string
     */
    public function getSerialization(): string
    {
        return $this->serialization;
    }

    /**
     * @param string $serialization
     */
    public function setSerialization(string $serialization): void
    {
        $this->serialization = $serialization;
    }

}
