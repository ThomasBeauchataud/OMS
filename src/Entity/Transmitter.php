<?php

/**
 * Author Thomas Beauchataud
 * Since 14/03/2021
 */


namespace App\Entity;


use App\Repository\TransmitterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TransmitterRepository::class)
 */
class Transmitter
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
    private string $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $alias;

    /**
     * @ORM\ManyToOne(targetEntity=Entity::class, fetch="EAGER")
     */
    private Entity $entity;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="transmitter")
     */
    private Collection $orders;

    /**
     * @ORM\OneToMany(targetEntity=TransmitterSender::class, mappedBy="transmitter")
     */
    private Collection $transmitterSenders;


    /*****************************************
     *****************************************
     ************** CONSTRUCTOR **************
     *****************************************
     *****************************************/


    /**
     * Transmitter constructor.
     */
    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->transmitterSenders = new ArrayCollection();
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     */
    public function setAlias(string $alias): void
    {
        $this->alias = $alias;
    }

    /**
     * @return Entity
     */
    public function getEntity(): Entity
    {
        return $this->entity;
    }

    /**
     * @return Collection
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    /**
     * @return Collection
     */
    public function getTransmitterSenders(): Collection
    {
        return $this->transmitterSenders;
    }

}
