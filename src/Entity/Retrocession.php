<?php


namespace App\Entity;


use App\Repository\RetrocessionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RetrocessionRepository::class)
 */
class Retrocession
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
     * @ORM\Column(type="boolean")
     */
    private bool $sent;

    /**
     * @ORM\OneToOne(targetEntity=Preparation::class, inversedBy="retrocession")
     */
    private Preparation $preparation;




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
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return bool
     */
    public function isSent(): bool
    {
        return $this->sent;
    }

    /**
     * @param bool $sent
     */
    public function setSent(bool $sent): void
    {
        $this->sent = $sent;
    }

    /**
     * @return Preparation
     */
    public function getPreparation(): Preparation
    {
        return $this->preparation;
    }

    /**
     * @param Preparation $preparation
     */
    public function setPreparation(Preparation $preparation): void
    {
        $this->preparation = $preparation;
    }
}
