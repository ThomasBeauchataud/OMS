<?php

/**
 * Author Thomas Beauchataud
 * Since 14/03/2021
 */


namespace App\Service;


use App\Entity\Preparation;
use App\Service\Stock\StockManagerInterface;
use App\Workflow\Preparation\PreparationWorkflowServiceInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PreparationWorkflowService implements PreparationWorkflowServiceInterface
{

    /**
     * @var ParameterBagInterface
     */
    protected ParameterBagInterface $parameterBag;

    /**
     * @var StockManagerInterface
     */
    protected StockManagerInterface $stockManager;

    /**
     * PreparationWorkflowService constructor.
     * @param ParameterBagInterface $parameterBag
     * @param StockManagerInterface $stockManager
     */
    public function __construct(ParameterBagInterface $parameterBag, StockManagerInterface $stockManager)
    {
        $this->parameterBag = $parameterBag;
        $this->stockManager = $stockManager;
    }


    /**
     * @inheritdoc
     */
    public function exportToPicker(Preparation $preparation): void
    {
        $directoryPath = $this->parameterBag->get('export.prep.folder') . "\\" . $preparation->getPicker()->getPreparer()->getAlias();
        $fileName = $this->parameterBag->get('export.prep.file');
        $content = $preparation->getProduct() . $preparation->getQuantity() . $preparation->getPicker()->getClient()->getAlias();
        FileWriter::writeFile($directoryPath, $fileName, array($content));
    }

    /**
     * @inheritdoc
     */
    public function updateRealStock(Preparation $preparation): void
    {
        $this->stockManager->updateRealStocks(
            $preparation->getOrderRow()->getOrder()->getTransmitter()->getEntity(),
            $preparation->getPicker()->getPreparer(),
            array($preparation->getProduct())
        );
    }

}