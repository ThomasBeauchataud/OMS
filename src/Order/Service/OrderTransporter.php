<?php


namespace App\Order\Service;


use App\Order\Entity\Order;
use Symfony\Component\Serializer\SerializerInterface;

class OrderTransporter implements OrderTransporterInterface
{

    /**
     * @var SerializerInterface
     */
    protected SerializerInterface $serializer;

    /**
     * OrderTransporter constructor.
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }


    /**
     * @param Order $order
     */
    public function transport(Order $order): void
    {
        $data = $this->serializer->serialize($order, 'json', array('groups' => 'order'));
        $file = fopen('order.txt', 'w+');
        fwrite($file, $data);
        fclose($file);
    }

}