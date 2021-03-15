<?php

/**
 * Author Thomas Beauchataud
 * Since 14/03/2021
 */


namespace App\Command\Import;


use App\Entity\Preparation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportPreparationCommand extends Command
{

    /**
     * @inheritdoc
     */
    protected static $defaultName = "app:import-prep";

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * OrderLoaderCommand constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }


    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var Preparation $preparation */
        $preparation = $this->em->getRepository(Preparation::class)->findOneBy(['sentQuantity' => null]);
        if ($preparation === null) {
            return self::SUCCESS;
        }
        $preparation->setSentQuantity($preparation->getQuantity());
        $this->em->persist($preparation);
        $this->em->flush();
        return self::SUCCESS;
    }

}