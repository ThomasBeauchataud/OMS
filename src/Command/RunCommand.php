<?php


namespace App\Command;


use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{

    /**
     * @inheritdoc
     */
    protected static $defaultName = 'app:run';

    /**
     * @inheritdoc
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $command = $this->getApplication()->find('app:import-order');
        if($command->run($input, $output) === self::SUCCESS) {
            $output->writeln("Orders imported");
        } else {
            $output->writeln("Orders importation failed");
        }

        $command = $this->getApplication()->find('app:import-delivery');
        if($command->run($input, $output) === self::SUCCESS) {
            $output->writeln("Deliveries imported");
        } else {
            $output->writeln("Deliveries importation failed");
        }

        $command = $this->getApplication()->find('app:proceed');
        if($command->run($input, $output) === self::SUCCESS) {
            $output->writeln("Workflow done");
        } else {
            $output->writeln("Workflow failed");
        }

        $command = $this->getApplication()->find('app:export-order');
        if($command->run($input, $output) === self::SUCCESS) {
            $output->writeln("Orders exported");
        } else {
            $output->writeln("Orders exportation failed");
        }

        return self::SUCCESS;
    }

}