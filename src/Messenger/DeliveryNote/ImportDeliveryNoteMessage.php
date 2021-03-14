<?php

/**
 * Author Thomas Beauchataud
 * From 14/03/2021
 */


namespace App\Messenger\DeliveryNote;


use App\Entity\DeliveryNote;

/**
 * An order importation message coming from a transporter
 */
class ImportDeliveryNoteMessage
{

    /**
     * @var DeliveryNote
     */
    protected DeliveryNote $deliveryNote;

    /**
     * OrderMessage constructor.
     * @param DeliveryNote $deliveryNote
     */
    public function __construct(DeliveryNote $deliveryNote)
    {
        $this->deliveryNote = $deliveryNote;
    }


    /**
     * @return DeliveryNote
     */
    public function getDeliveryNote(): DeliveryNote
    {
        return $this->deliveryNote;
    }

}