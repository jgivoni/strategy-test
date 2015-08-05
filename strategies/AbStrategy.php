<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'strategies/Strategy.php';

class AbStrategy extends Strategy
{
    public $name = 'AB even';

    /**
     * Always returns equal weights on templates
     * 
     * @param array $visits Of int per experience
     * @param array $conversions Of int per experience
     * @param array $revenue Of float per experience
     * @return array Of int/float per experience
     */
    public function getWeights($visits, $conversions, $xSales, $revenue, $stdev, $revPerConvStdev)
    {
        $experiences = count($visits);
        $weight = (float) 10000 / (float) $experiences;
        return array_pad([], $experiences, $weight);
    }

}
