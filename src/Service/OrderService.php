<?php


namespace App\Service;


use App\Entity\Order;
use App\Entity\Sender;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class OrderService implements OrderExporterInterface, OrderRendererInterface, OrderValidatorInterface, SenderSelectorInterface, PreparationCreatorInterface
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
     * OrderService constructor.
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     */
    public function __construct(SerializerInterface $serializer, EntityManagerInterface $em)
    {
        $this->serializer = $serializer;
        $this->em = $em;
    }


    /**
     * @inheritDoc
     */
    public function exportOrder(Order $order): void
    {
        $data = $this->serializer->serialize($order, 'json', array('groups' => 'order'));
        $file = fopen('order.txt', 'w+');
        fwrite($file, $data);
        fclose($file);
    }

    /**
     * @inheritDoc
     */
    public function render(Order $order): void
    {

    }

    /**
     * @inheritDoc
     */
    public function validateStock(Order $order): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function forceExportation(Order $order): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function selectSender(Order $order): Sender
    {
        /** @var Sender $sender */
        $sender = $this->em->getRepository(Sender::class)->findOneBy([]);
        return $sender;
    }

    /**
     * @param Order $order
     */
    public function createPreparations(Order $order): void
    {
        // TODO: Implement createPreparations() method.
    }
}