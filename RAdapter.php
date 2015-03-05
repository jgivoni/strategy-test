<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class RAdapter
{
    public function execute($command)
    {
        $output = [];
        $status = null;
        exec("R -e '{$command}' --slave --vanilla", $output, $status);
        if (empty($status)) {
            // Each element in the output is a row of data
            $rows = array_map(function($line) {
                // Remove the line number
                $columns = array_slice(explode(' ', $line), 1);
                // Convert to floats
                return array_map(function($column) {
                    return (float) $column;
                }, $columns);
            }, $output);
            return $rows;
        } else {
            echo "R command failed:\n";
            echo $command . "\n\n";
            return null;
        }
    }

}
