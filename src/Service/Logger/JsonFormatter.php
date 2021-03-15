<?php

/**
 * Author Thomas Beauchataud
 * From 15/03/2021
 */


namespace App\Service\Logger;


use Monolog\Formatter\FormatterInterface;

class JsonFormatter implements FormatterInterface
{

    /**
     * @inheritDoc
     */
    public function format(array $record)
    {
        return json_encode($record);
    }

    /**
     * @inheritDoc
     */
    public function formatBatch(array $records)
    {
        return json_encode($records);
    }

}