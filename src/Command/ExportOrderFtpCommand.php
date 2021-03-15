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
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ExportOrderFtpCommand extends Command
{

    public const FILE_NAME = "orders.csv";

    /**
     * @inheritdoc
     */
    protected static $defaultName = "app:export-order";

    /**
     * @var ParameterBagInterface
     */
    protected ParameterBagInterface $parameterBag;

    /**
     * ExportOrderFtpCommand constructor.
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(ParameterBagInterface $parameterBag)
    {
        parent::__construct();
        $this->parameterBag = $parameterBag;
    }


    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {

            $connexion = ftp_connect($this->parameterBag->get("ftp.server"));
            $user = $this->parameterBag->get("ftp.user");
            $password = $this->parameterBag->get("ftp.password");

            ftp_login($connexion, $user, $password);
            $output->writeln(self::FILE_NAME . " exported on ftp server");

            if (ftp_put($connexion, self::FILE_NAME, self::FILE_NAME, FTP_ASCII)) {
                $output->writeln(self::FILE_NAME . " exported on ftp server");
            } else {
                $output->writeln(self::FILE_NAME . " failed to upload on ftp server");
            }
            ftp_close($connexion);

        } catch (Exception $e) {

            $output->writeln($e->getMessage());
            return self::FAILURE;

        }

        return self::SUCCESS;
    }

}