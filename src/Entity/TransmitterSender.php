<?php

/**
 * Author Thomas Beauchataud
 * Since 14/03/2021
 */


namespace App\Entity;


use App\Repository\TransmitterSenderRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * The mapping between transmitter and sender
 * This mapping and his priorities are used the choose the sender of an order during his initialisation
 *
 * @ORM\Entity(repositoryClass=TransmitterSenderRepository::class)
 */
class TransmitterSender
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
     * @ORM\ManyToOne(targetEntity=Transmitter::class, inversedBy="transmitterSenders")
     */
    private Transmitter $transmitter;

    /**
     * @ORM\ManyToOne(targetEntity=Sender::class)
     */
    private Sender $sender;


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
     * @return Transmitter
     */
    public function getTransmitter(): Transmitter
    {
        return $this->transmitter;
    }

    /**
     * @param Transmitter $transmitter
     */
    public function setTransmitter(Transmitter $transmitter): void
    {
        $this->transmitter = $transmitter;
    }

    /**
     * @return Sender
     */
    public function getSender(): Sender
    {
        return $this->sender;
    }

    /**
     * @param Sender $sender
     */
    public function setSender(Sender $sender): void
    {
        $this->sender = $sender;
    }
}
