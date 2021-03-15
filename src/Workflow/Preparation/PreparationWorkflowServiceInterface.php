<?php

/**
 * Author Thomas Beauchataud
 * Since 14/03/2021
 */


namespace App\Workflow\Preparation;


use App\Entity\Preparation;

interface PreparationWorkflowServiceInterface
{

    /**
     * Export a preparation to the picker
     *
     * @param Preparation $preparation
     */
    public function exportToPicker(Preparation $preparation): void;

    /**
     * Update available picker stock (realStock) after having associated a preparation to the picker
     *
     * @param Preparation $preparation
     */
    public function updateRealStock(Preparation $preparation): void;

}