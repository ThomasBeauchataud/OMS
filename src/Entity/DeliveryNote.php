<?php

/**
 * Author Thomas Beauchataud
 * Since 14/03/2021
 */


namespace App\Entity;


use App\Repository\DeliveryNoteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * A delivery note received from the sender when the order has been delivery
 * It will be next send back to the transmitter of the order to close it
 *
 * @ORM\Entity(repositoryClass=DeliveryNoteRepository::class)
 */
class DeliveryNote
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
     * @ORM\OneToOne(targetEntity=Order::class, mappedBy="deliveryNote")
     */
    private Order $order;


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
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

}
