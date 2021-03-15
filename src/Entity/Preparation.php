<?php

/**
 * Author Thomas Beauchataud
 * Since 14/03/2021
 */


namespace App\Entity;


use App\Repository\PreparationRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PreparationRepository::class)
 */
class Preparation
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
     * @ORM\Column(type="string", length=255)
     */
    private string $product;

    /**
     * @ORM\Column(type="integer")
     */
    private int $quantity;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $sentQuantity;

    /**
     * @ORM\ManyToOne(targetEntity=Picker::class)
     */
    private Picker $picker;

    /**
     * @ORM\OneToOne(targetEntity=OrderRow::class, mappedBy="preparation", fetch="EAGER")
     */
    private OrderRow $orderRow;


    /*****************************************
     *****************************************
     ********** WORKFLOW ATTRIBUTES **********
     *****************************************
     *****************************************/


    /**
     * @ORM\Column(type="boolean")
     */
    private bool $closed;

    /**
     * @ORM\Column(type="json")
     */
    private array $state;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTimeInterface $lastUpdate;


    /*****************************************
     *****************************************
     ************** CONSTRUCTOR **************
     *****************************************
     *****************************************/


    /**
     * Preparation constructor.
     */
    public function __construct()
    {
        $this->sentQuantity = null;
        $this->closed = false;
        $this->state = array();
        $this->lastUpdate = new DateTime();
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
     * @return int|null
     */
    public function getSentQuantity(): ?int
    {
        return $this->sentQuantity;
    }

    /**
     * @param int $sentQuantity
     */
    public function setSentQuantity(int $sentQuantity): void
    {
        $this->sentQuantity = $sentQuantity;
    }

    /**
     * @return Picker
     */
    public function getPicker(): Picker
    {
        return $this->picker;
    }

    /**
     * @param Picker $picker
     */
    public function setPicker(Picker $picker): void
    {
        $this->picker = $picker;
    }

    /**
     * @return OrderRow
     */
    public function getOrderRow(): OrderRow
    {
        return $this->orderRow;
    }

    /**
     * @param OrderRow $orderRow
     */
    public function setOrderRow(OrderRow $orderRow): void
    {
        $this->orderRow = $orderRow;
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

}
