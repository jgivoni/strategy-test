<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'strategies/Strategy.php';

class MyCrStrategy extends Strategy
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
    public function getWeights($visits, $conversions, $xSales, $revenue, $stdev, $revPerConvStdev)
    {
        $experiences = count($visits);
        $highGroup = [];
        $highCr = 0;
        foreach ($conversions as $i => $c) {
            $cr = $visits[$i] > 0 ? (float) $c / (float) $visits[$i] : 0;
            if ($cr > $highCr) {
                $highCr = $cr;
                // Put $i as the only member of the high group
                $highGroup = [$i];
            } elseif ($cr == $highCr) {
                // Add $i to the high group
                $highGroup[] = $i;
            }
        }
        $weights = [];
        foreach ($visits as $i => $v) {
            if (in_array($i, $highGroup)) {
                $weights[] = 8001 / count($highGroup);
            } else {
                $weights[] = 1999 / ($experiences - count($highGroup));
            }
        }
           
        return $weights;
    }

}
