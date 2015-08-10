<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'strategies/Strategy.php';

class MyStrategy extends Strategy
{
    public $name = 'Custom 80% to optimal';
    
    /**
     * Always returns 80% weight to the currently 'best' experience
     * 
     * @param array $visits Of int per experience
     * @param array $conversions Of int per experience
     * @param array $revenue Of float per experience
     * @return array Of int/float per experience
     */
    public function getWeights($visits, $conversions, $xSales, $revenue, $sumSqRev)
    {
        $experiences = count($visits);
        $highCr = 0;
        foreach ($conversions as $i => $c) {
            $cr = (float) $c / (float) $visits[$i];
            if ($cr >= $highCr) {
                $highCr = $cr;
                $winI = $i;
            }
        }
        $weights = [];
        foreach ($visits as $i => $v) {
            if ($i == $winI) {
                $weights[] = 80000;
            } else {
                $weights[] = 20000 / ($experiences - 1);
            }
        }
           
        return $weights;
    }

}
