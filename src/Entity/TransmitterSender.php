<?php


namespace App\Entity;


use App\Repository\TransmitterSenderRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TransmitterSenderRepository::class)
 */
class TransmitterSender
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
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
