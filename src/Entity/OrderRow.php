<?php

/**
 * Author Thomas Beauchataud
 * Since 14/03/2021
 */


namespace App\Entity;


use App\Repository\OrderRowRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * The mapping between an order and his products
 *
 * @ORM\Entity(repositoryClass=OrderRowRepository::class)
 */
class OrderRow
{

    /*****************************************
     *****************************************
     ************** ATTRIBUTES ***************
     *****************************************
     *****************************************/


    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotNull()
     */
    private string $product;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotNull()
     */
    private string $ean;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotNull()
     */
    private int $quantity;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $medicine;

    /**
     * @ORM\OneToOne(targetEntity=Preparation::class, mappedBy="orderRow", fetch="EAGER", cascade={"persist"})
     */
    private ?Preparation $preparation;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="orderRows")
     */
    private Order $order;

    /**
     * @ORM\Column(type="string", length=65535)
     */
    private string $serialization;


    /*****************************************
     *****************************************
     ************** CONSTRUCTOR **************
     *****************************************
     *****************************************/


    /**
     * OrderRow constructor.
     */
    public function __construct()
    {
        $this->medicine = false;
        $this->preparation = null;
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
     * @return bool
     */
    public function isMedicine(): bool
    {
        return $this->medicine;
    }

    /**
     * @param bool $medicine
     */
    public function setMedicine(bool $medicine): void
    {
        $this->medicine = $medicine;
    }

    /**
     * @return Preparation|null
     */
    public function getPreparation(): ?Preparation
    {
        return $this->preparation;
    }

    /**
     * @param Preparation $preparation
     */
    public function setPreparation(Preparation $preparation): void
    {
        $this->preparation = $preparation;
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
