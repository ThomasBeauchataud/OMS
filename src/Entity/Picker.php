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

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Sender::class, inversedBy="pickers")
     */
    private Sender $client;

    /**
     * @ORM\ManyToOne(targetEntity=Sender::class, inversedBy="client")
     */
    private Sender $preparer;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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
    
}