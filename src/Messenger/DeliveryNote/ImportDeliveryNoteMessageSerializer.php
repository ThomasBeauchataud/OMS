<?php

/**
 * Author Thomas Beauchataud
 * From 14/03/2021
 */


namespace App\Messenger\DeliveryNote;


use App\Entity\DeliveryNote;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface as TransportSerializerInterface;

/**
 * Serialize and deserialize delivery note importation message between transporters and message handler
 */
class ImportDeliveryNoteMessageSerializer implements TransportSerializerInterface
{

    /**
     * @var SerializerInterface
     */
    protected SerializerInterface $serializer;

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * OrderSerializer constructor.
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     */
    public function __construct(SerializerInterface $serializer, EntityManagerInterface $em)
    {
        $this->serializer = $serializer;
        $this->em = $em;
    }


    /**
     * @param array $encodedEnvelope
     * @return Envelope
     */
    public function decode(array $encodedEnvelope): Envelope
    {
        /** @var DeliveryNote $deliveryNote */
        $deliveryNote = $this->serializer->deserialize($encodedEnvelope["body"], DeliveryNote::class, 'json');
        return new Envelope(new ImportDeliveryNoteMessage($deliveryNote));
    }

    /**
     * @param Envelope $envelope
     * @return array
     */
    public function encode(Envelope $envelope): array
    {
        return array(
            'body' => $this->serializer->serialize($envelope->getMessage(), 'json', array('groups' => 'deliveryNote')),
            'headers' => array()
        );
    }

}