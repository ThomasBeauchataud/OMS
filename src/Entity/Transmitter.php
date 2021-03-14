<?php


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
     * @ORM\OneToMany(targetEntity=TransmitterSender::class, mappedBy="transmitter")
     */
    private Collection $transmitterSenders;

    /**
     * Transmitter constructor.
     */
    public function __construct()
    {
        $this->transmitterSenders = new ArrayCollection();
    }

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
    public function getTransmitterSenders(): Collection
    {
        return $this->transmitterSenders;
    }

}
