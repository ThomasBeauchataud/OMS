<?php

/**
 * Author Thomas Beauchataud
 * Since 14/03/2021
 */


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
        while (true) {
            /*$command = $this->getApplication()->find('app:import-order');
            if ($command->run($input, $output) === self::SUCCESS) {
                $output->writeln("Orders imported");
            } else {
                $output->writeln("Orders importation failed");
            }*/

            $command = $this->getApplication()->find('app:import-delivery');
            if ($command->run($input, $output) === self::SUCCESS) {
                $output->writeln("Deliveries imported");
            } else {
                $output->writeln("Deliveries importation failed");
            }

            $command = $this->getApplication()->find('app:import-prep');
            if ($command->run($input, $output) === self::SUCCESS) {
                $output->writeln("Preparations imported.");
            } else {
                $output->writeln("Preparations importation failed.");
            }

            $command = $this->getApplication()->find('app:proceed-order');
            if ($command->run($input, $output) === self::SUCCESS) {
                $output->writeln("Workflow done");
            } else {
                $output->writeln("Workflow failed");
            }

            $command = $this->getApplication()->find('app:proceed-prep');
            if ($command->run($input, $output) === self::SUCCESS) {
                $output->writeln("Workflow done");
            } else {
                $output->writeln("Workflow failed");
            }

            sleep(10);
        }
        return self::SUCCESS;
    }

}