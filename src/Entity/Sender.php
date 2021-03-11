<?php


namespace App\Entity;


use App\Repository\SenderRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SenderRepository::class)
 */
class Sender
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

}
