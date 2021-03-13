<?php


namespace App\Entity;


use App\Repository\PreparationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PreparationRepository::class)
 */
class Preparation extends WorkflowPreparation
{

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
     * @ORM\ManyToOne(targetEntity=Sender::class)
     */
    private Picker $picker;

    /**
     * @ORM\ManyToOne(targetEntity=Sender::class)
     */
    private Sender $client;

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
     * @return Sender
     */
    public function getClient(): Sender
    {
        return $this->client;
    }

    /**
     * @param Sender $client
     */
    public function setClient(Sender $client): void
    {
        $this->client = $client;
    }

}
