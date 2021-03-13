<?php


namespace App\Entity;


use App\Repository\SenderRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SenderRepository::class)
 */
class Sender implements Picker, FtpActor
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $medicineManager;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $folder;

    /**
     * @ORM\ManyToOne(targetEntity=Sender::class, inversedBy="pickers")
     * @ORM\JoinColumn(name="picker", referencedColumnName="id")
     */
    private Sender $client;

    /**
     * @ORM\OneToMany(targetEntity=Sender::class, mappedBy="client")
     */
    private Collection $pickers;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFolder(): string
    {
        return $this->folder;
    }

    /**
     * @return bool
     */
    public function isMedicineManager(): bool
    {
        return $this->medicineManager;
    }

    /**
     * @param bool $medicineManager
     */
    public function setMedicineManager(bool $medicineManager): void
    {
        $this->medicineManager = $medicineManager;
    }

    /**
     * @return Collection
     */
    public function getPickers(): Collection
    {
        return $this->pickers;
    }

}
