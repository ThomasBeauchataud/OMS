<?php

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
    private $id;

    private Order $order;

    public function getId(): ?int
    {
        return $this->id;
    }
}
