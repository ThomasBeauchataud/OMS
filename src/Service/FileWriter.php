<?php

/**
 * Author Thomas Beauchataud
 * From 15/03/2021
 */


namespace App\Service;


class FileWriter
{

    /**
     * Just write in a file
     * Create folders if they doesn't exist
     *
     * @param string $directoryPath
     * @param string $fileName
     * @param array $rows
     */
    public static function writeFile(string $directoryPath, string $fileName, array $rows): void
    {
        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 777, true);
        }
        $file = fopen("$directoryPath\\$fileName", 'a');
        foreach ($rows as $row) {
            fwrite($file, $row . "\n");
        }
        fclose($file);
    }

}