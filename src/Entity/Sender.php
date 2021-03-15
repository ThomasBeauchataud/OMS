<?php

/**
 * Author Thomas Beauchataud
 * Since 14/03/2021
 */


namespace App\Entity;


use App\Repository\SenderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SenderRepository::class)
 */
class Sender
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
     * @ORM\Column(type="boolean")
     */
    private bool $medicineManager;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="sender", fetch="EAGER")
     */
    private Collection $orders;

    /**
     * @ORM\OneToMany(targetEntity=Picker::class, mappedBy="client", fetch="EAGER")
     */
    private Collection $pickers;

    /**
     * @ORM\OneToMany(targetEntity=Picker::class, mappedBy="preparer", fetch="EAGER")
     */
    private Collection $clients;


    /*****************************************
     *****************************************
     ************** CONSTRUCTOR **************
     *****************************************
     *****************************************/


    /**
     * Sender constructor.
     */
    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->pickers = new ArrayCollection();
        $this->clients = new ArrayCollection();
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
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @return bool
     */
    public function isMedicineManager(): bool
    {
        return $this->medicineManager;
    }

    /**
     * @param bool $medicineManager
     */
    public function setMedicineManager(bool $medicineManager): void
    {
        $this->medicineManager = $medicineManager;
    }

    /**
     * @return Collection
     */
    public function getPickers(): Collection
    {
        return $this->pickers;
    }

    /**
     * @return Collection
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }

    /**
     * @param Collection $clients
     */
    public function setClients(Collection $clients): void
    {
        $this->clients = $clients;
    }

}
