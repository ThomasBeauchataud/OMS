<?php

/**
 * Author Thomas Beauchataud
 * From 14/03/2021
 */


namespace App\Entity;


use App\Repository\SenderRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SenderRepository::class)
 */
class Sender
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
     * @ORM\OneToMany(targetEntity=Picker::class, mappedBy="client", fetch="EAGER")
     */
    private Collection $pickers;

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

}
