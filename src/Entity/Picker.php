<?php

/**
 * Author Thomas Beauchataud
 * Since 14/03/2021
 */


namespace App\Entity;


use App\Repository\PickerRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PickerRepository::class)
 */
class Picker
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
     * 1 the highest priority
     *
     * @ORM\Column(type="integer")
     */
    private int $priority;

    /**
     * @ORM\ManyToOne(targetEntity=Sender::class, inversedBy="pickers")
     */
    private Sender $client;

    /**
     * @ORM\ManyToOne(targetEntity=Sender::class, inversedBy="client")
     */
    private Sender $preparer;

    /**
     * @ORM\ManyToOne(targetEntity=Entity::class)
     */
    private Entity $clientEntity;

    /**
     * @ORM\ManyToOne(targetEntity=Entity::class)
     */
    private Entity $preparerEntity;


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
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     */
    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
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

    /**
     * @return Sender
     */
    public function getPreparer(): Sender
    {
        return $this->preparer;
    }

    /**
     * @param Sender $preparer
     */
    public function setPreparer(Sender $preparer): void
    {
        $this->preparer = $preparer;
    }

    /**
     * @return Entity
     */
    public function getClientEntity(): Entity
    {
        return $this->clientEntity;
    }

    /**
     * @param Entity $clientEntity
     */
    public function setClientEntity(Entity $clientEntity): void
    {
        $this->clientEntity = $clientEntity;
    }

    /**
     * @return Entity
     */
    public function getPreparerEntity(): Entity
    {
        return $this->preparerEntity;
    }

    /**
     * @param Entity $preparerEntity
     */
    public function setPreparerEntity(Entity $preparerEntity): void
    {
        $this->preparerEntity = $preparerEntity;
    }
    
}