<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class Strategy {

    /**
     * Set name in subtypes
     * @var string
     */
    public $name = 'N/A';

    /**
     * Returns the weights for each experience
     */
    abstract public function getWeights($visits, $conversions, $xSales, $revenue, $stdev, $revPerConvStdev, $sumSqRev);

    public function displayConfig() {
        return "";
    }

}
