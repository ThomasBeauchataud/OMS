<?php

/**
 * Author Thomas Beauchataud
 * Since 14/03/2021
 */


namespace App\Command;


use App\Entity\Preparation;
use App\Workflow\WorkflowRunner;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProceedPreparationCommand extends Command
{

    /**
     * @inheritdoc
     */
    protected static $defaultName = "app:proceed-prep";

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * @var WorkflowRunner
     */
    protected WorkflowRunner $workflow;

    /**
     * OrderLoaderCommand constructor.
     * @param EntityManagerInterface $em
     * @param WorkflowRunner $preparationWorkflow
     */
    public function __construct(EntityManagerInterface $em, WorkflowRunner $preparationWorkflow)
    {
        parent::__construct();
        $this->em = $em;
        $this->workflow = $preparationWorkflow;
    }


    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $preparations = $this->em->getRepository(Preparation::class)->findBy(['closed' => false]);
        $total = count($preparations);
        $output->writeln("Running workflow on $total preparations.");
        $count = 0;
        /** @var Preparation $preparation */
        foreach($preparations as $preparation) {
            $count += 1;
            $this->workflow->proceedPreparation($preparation);
            $percent = intval(($count / $total) * 100);
            $output->write("\r$percent%");
        }
        return self::SUCCESS;
    }

}