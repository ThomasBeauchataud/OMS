<?php


namespace App\Repository;


use App\Entity\Picker;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Picker|null find($id, $lockMode = null, $lockVersion = null)
 * @method Picker|null findOneBy(array $criteria, array $orderBy = null)
 * @method Picker[]    findAll()
 * @method Picker[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PickerRepository extends ServiceEntityRepository
{

    /**
     * PickerRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Picker::class);
    }

}
