<?php

/**
 * Author Thomas Beauchataud
 * From 14/03/2021
 */


namespace App\Messenger\Order;


use App\Entity\Order;
use App\Entity\Transmitter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface as TransportSerializerInterface;

/**
 * Serialize and deserialize order message between transporters and message handler
 */
class ImportOrderMessageSerializer implements TransportSerializerInterface
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
        /** @var Order $order */
        $order = $this->serializer->deserialize($encodedEnvelope["body"], Order::class, 'json');
        /** @var Transmitter $transmitter */
        $transmitter = $this->em->getRepository(Transmitter::class)->findOneBy(["alias" => $encodedEnvelope["headers"]["transmitter"]]);
        if ($transmitter !== null) {
            $order->setTransmitter($transmitter);
        }
        return new Envelope(new ImportOrderMessage($order));
    }

    /**
     * @param Envelope $envelope
     * @return array
     */
    public function encode(Envelope $envelope): array
    {
        return array(
            'body' => $this->serializer->serialize($envelope->getMessage(), 'json', array('groups' => 'order')),
            'headers' => array()
        );
    }

}