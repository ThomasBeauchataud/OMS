<?php


namespace App\Actor\Service;


use App\Order\Entity\Order;
use App\Actor\Entity\Sender;
use Doctrine\ORM\EntityManagerInterface;

class SenderRuler implements SenderRulerInterface
{

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * SenderRuler constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    /**
     * @inheritdoc
     */
    public function chooseSender(Order $order): Sender
    {
        /** @var Sender $sender */
        $sender = $this->em->getRepository(Sender::class)->findOneBy([]);
        return $sender;
    }

}