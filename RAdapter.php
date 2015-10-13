<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class RAdapter {

    const FORMAT_VECTOR = 1;
    const FORMAT_DATAFRAME = 2;
    const FORMAT_CSV = 3;

    public function execute($command, $outputFormat = self::FORMAT_VECTOR) {
        $output = [];
        $status = null;
        if (strpos($command, "\n") === false) {
            // Single line command
            exec("R -e '{$command}' --slave --vanilla", $output, $status);
        } else {
            // Multiple line script
            $tmpRScript = sys_get_temp_dir() . '/' . uniqid() . '.r';
            file_put_contents($tmpRScript, $command);
            exec("R -f '{$tmpRScript}' --slave --vanilla", $output, $status);
            unlink($tmpRScript);
        }
        if (empty($status)) {
            if ($outputFormat == self::FORMAT_VECTOR) {
                // Output is a vector (what is the line lenght?)
                $rows = array_map(function($line) {
                    // Remove the line number
                    $columns = array_slice(explode(' ', $line), 1);
                    // Convert to floats
                    return array_map(function($column) {
                        return (float) $column;
                    }, $columns);
                }, $output);
                return $rows;
            } elseif ($outputFormat == self::FORMAT_DATAFRAME) {
                // Output is a dataframe?
                $output = array_slice($output, 2);
                $rows = array_map(function($line) {
                    $columns = explode(' ', $line);
                    // Convert to floats
                    return array_map(function($column) {
                        return (float) $column;
                    }, $columns);
                }, $output);
                return $rows;
            } elseif ($outputFormat == self::FORMAT_CSV) {
                $rows = array_map(function($line) {
                    $columns = explode(' ', $line);
                    // Convert to floats
                    return array_map(function($column) {
                        return (float) $column;
                    }, $columns);
                }, $output);
                return $rows;
            } else {
                return $output;
            }
        } else {
            echo "R command failed:\n";
            echo $command . "\n\n";
            return null;
        }
    }

}
