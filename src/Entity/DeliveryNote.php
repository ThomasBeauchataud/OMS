<?php

/**
 * Author Thomas Beauchataud
 * From 14/03/2021
 */


namespace App\Entity;


use App\Entity\Order;
use App\Repository\DeliveryNoteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DeliveryNoteRepository::class)
 */
class DeliveryNote
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @var \App\Entity\Order
     */
    private Order $order;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}
